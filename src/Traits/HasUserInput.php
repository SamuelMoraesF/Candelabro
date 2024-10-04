<?php

namespace Candelabro\Traits;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;

trait HasUserInput
{
    use HasUserConfig;

    protected function persistUserInput(array $input): void
    {
        $paramsToPersist = array_filter(static::getParams(), fn ($options) => $options['persist'] ?? false);
        $filteredInput = array_intersect_key($input, $paramsToPersist);

        foreach ($filteredInput as $param => $value) {
            $this->setUserConfig($param, $value);
        }
    }

    protected function getInput(InputInterface $input): array
    {
        $inputData = [];

        foreach (static::getParams() as $param => $options) {
            $inputData[$param] = $this->getInputParam($input, $param, $options);
        }

        return $inputData;
    }

    protected function askParamInput(array $options): mixed
    {
        if (isset($options['options'])) {
            return select(
                label: $options['label'],
                hint: $options['description'],
                options: $options['options'],
                required: true,
            );
        }

        $function = ($options['textarea'] ?? null === true)
            ? textarea(...)
            : text(...);

        return $function(
            label: $options['label'],
            hint: $options['description'],
            required: true,
            validate: $options['validate'] ?? null,
        );
    }

    protected function validateParam(array $options, mixed $value): void
    {
        if (!isset($options['validate'])) {
            return;
        }

        $error = $options['validate']($value);

        if ($error !== null) {
            throw new InvalidArgumentException($error);
        }
    }

    protected function getInputParam(InputInterface $input, string $param, array $options): mixed
    {
        $value = $input->getOption($param)
            ?? $this->getUserConfig($param)
            ?? $this->askParamInput($options);

        $this->validateParam($options, $value);

        return $value;
    }
}
