<?php
namespace PocketProX\Network;

class Protocol {
    const MINECRAFT_VERSION = "v1.21.30";
    const SERVER_VERSION = "alpha.1.0.0";
    const PROTOCOL_VERSION = 729;

    const HANDSHAKE_PACKET = 0x01;
    const PLAY_PACKET = 0x02;
    const BLOCK_PLACE_PACKET = 0x03;
    const BLOCK_BREAK_PACKET = 0x04;
    const COMMAND_PACKET = 0x05;
    const RESPONSE_PACKET = 0x06;
    const DISCONNECT_PACKET = 0x06;
    const ADD_PLAYER_PACKET = 0x07;
    const TEXT_PACKET = 0x08;
    const MOVE_PLAYER_PACKET = 0x09;

    public static function encodePacket($type, $data) {
        return json_encode([
            'type' => $type,
            'data' => $data
        ]);
    }

    public static function decodePacket($packet) {
        return json_decode($packet, true);
    }
}