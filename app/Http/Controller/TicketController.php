<?php

namespace App\Http\Controller;

use App\Router\Response;
use App\Router\Validate;
use Database\Connection\DB;

class TicketController
{
    static function index($request)
    {
        $params = $request->getBody();
        try {
            $DB = new DB();
            $query = $DB->table('tickets')
                ->pagination($params['per_page']??100, $params['page']??1)
                ->select(['event_id', 'ticket_code, status']);
            if(isset($params['event_id'])) {
                $query->where('event_id', $params['event_id']);
            }

            if(isset($params['ticket_code'])) {
                $query->where('ticket_code', $params['ticket_code']);
            }

            $result = $query->get();
            return Response::json(200, $result);
        } catch (\Exception $e) {
            $result = ["status" => false, "message" => $e->getMessage()];
            return Response::json(500, $result);
        }
    }

    static function update($request) {
        $form = new Validate();
        $form->rules($request->getBody(), array(
            'event_id' => array('required' => 'This attribute is required'),
            'ticket_code' => array('required' => 'This attribute is required'),
            'status' => array('required' => 'This attribute is required'),
        ));

        if ($form->getMessages()) {
            $result = ["status" => false, "message" => $form->getMessages()];
            return Response::json(400, $result);
        }

        try {
            $DB = new DB();
            $data = $request->getBody();
            $result = $DB->table('tickets')
                ->where('event_id', $data['event_id'])
                ->where('ticket_code', $data['ticket_code'])
                ->update(['status' => $data['status'],
                    'updated_at' => (new \DateTime('now'))->format('Y-m-d H:i:s')]);

            $response = ["status" => false, "message" => "FAILED UPDATE DATA"];
            if ($result) {
                $response = $DB->table('tickets')
                    ->select(['event_id', 'ticket_code, status', 'updated_at'])
                    ->where('event_id', $data['event_id'])
                    ->where('ticket_code', $data['ticket_code'])
                    ->first();
            }

            if (is_array($response) && count($response) === 0) return Response::json(404, ["status" => false, "message" => "Data not found!"]);
            return Response::json(200, $response);
        } catch (\Exception $e) {
            $result = ["status" => false, "message" => $e->getMessage()];
            return Response::json(500, $result);
        }
    }
}
