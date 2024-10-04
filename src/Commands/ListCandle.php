<?php

namespace Candelabro\Commands;

use function Laravel\Prompts\error;
use function Laravel\Prompts\table;

use Candelabro\Enums\CandleColor;
use Candelabro\Services\Church;
use Candelabro\Traits\HasUserInput;
use Candelabro\ValueObject\Pray;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'candle:list', description: 'List all lit candles')]
class ListCandle extends Command
{
    use HasUserInput;

    protected static function getParams(): array
    {
        return [
            'email' => [
                'description' => 'Your email address.',
                'label' => 'Email',
                'validate' => static::validateEmail(...),
                'persist' => true,
            ],
        ];
    }

    protected static function validateEmail(string $email): ?string
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return 'Invalid email address.';
        }

        return null;
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to list all lit candles.');

        foreach (static::getParams() as $param => $options) {
            $this->addOption(
                name: $param,
                mode: InputArgument::OPTIONAL,
                description: $options['description'],
                suggestedValues: array_keys($options['options'] ?? []),
            );
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $input = $this->getInput($input);
        } catch (InvalidArgumentException $exception) {
            error($exception->getMessage());
            return Command::INVALID;
        }

        $list = $this->getPrayList($input['email']);

        if (count($list) === 0) {
            $output->writeln('No candles lit yet.');
            return Command::SUCCESS;
        }

        table(
            headers: ['Date', 'Color', 'Name', 'City', 'Days'],
            rows: static::formatTable($list),
        );

        return Command::SUCCESS;
    }

    protected static function formatTable(array $list): array
    {
        return array_map(
            fn (Pray $pray) => [
                $pray->date,
                ucwords($pray->color->value),
                $pray->name,
                $pray->city,
                $pray->days,
            ],
            $list,
        );
    }

    protected function getPrayList(string $email): array
    {
        $list = [];

        foreach (CandleColor::cases() as $color) {
            $colorList = (new Church())
                ->prayList(
                    email: $email,
                    color: $color,
                );

            $list = array_merge($list, $colorList);
        }

        return $list;
    }
}
