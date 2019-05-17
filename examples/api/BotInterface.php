<?php

namespace API;

interface BotInterface
{
    /**
     * @param string $lot
     * @param string $company
     * @param string $account
     * @return void
     * @throws \RuntimeException
     */
    public function upload(string $lot, string $company, string $account): void;
}
