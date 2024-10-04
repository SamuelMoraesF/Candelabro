<?php

namespace Candelabro\Traits;

trait HasUserConfig
{
    protected array $config;

    protected function getUserConfig(string $config): mixed
    {
        if (!isset($this->config)) {
            $this->config = $this->loadConfig();
        }

        return $this->config[$config] ?? null;
    }

    protected function loadConfig(): array
    {
        $configPath = $this->getConfigPath();

        if (!file_exists($configPath)) {
            return [];
        }

        return json_decode(file_get_contents($configPath), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function setUserConfig(string $config, mixed $value): void
    {
        $this->config[$config] = $value;

        file_put_contents($this->getConfigPath(), json_encode($this->config, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    protected function getConfigPath(): string
    {
        $fileName = '.castical.json';

        if (stripos(PHP_OS, 'WIN') === 0) {
            return getenv('APPDATA') . DIRECTORY_SEPARATOR . $fileName;
        }

        return getenv('HOME') . DIRECTORY_SEPARATOR . $fileName;
    }
}
