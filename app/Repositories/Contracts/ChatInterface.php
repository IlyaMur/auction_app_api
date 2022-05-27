<?php

namespace App\Repositories\Contracts;

interface ChatInterface
{
    public function createParticipans(int $chatId, array $data);
    public function getUserChats();
}
