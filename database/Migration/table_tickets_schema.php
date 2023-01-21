<?php
const STATEMENTS = [
        "SET NAMES utf8mb4;
        SET FOREIGN_KEY_CHECKS = 0;
        DROP TABLE IF EXISTS tickets;
        CREATE TABLE `tickets`  (
            `id`            bigint UNSIGNED     NOT NULL    AUTO_INCREMENT,
            `event_id`      bigint UNSIGNED     NOT NULL,
            `ticket_code`   varchar(255)        NOT NULL    UNIQUE,
            `status`        enum('available','claimed')     NOT NULL    DEFAULT 'available',
            `created_at`    DATETIME            NULL        DEFAULT CURRENT_TIMESTAMP,
            `updated_at`    DATETIME            NULL        DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`) USING BTREE
        )
        ENGINE = InnoDB
        AUTO_INCREMENT = 1
        CHARACTER SET = utf8mb4
        COLLATE = utf8mb4_unicode_ci
        ROW_FORMAT = DYNAMIC;
        SET FOREIGN_KEY_CHECKS = 1;
        "
];
