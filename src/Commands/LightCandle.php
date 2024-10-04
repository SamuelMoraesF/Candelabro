<?php

namespace Candelabro\Commands;

use function Laravel\Prompts\error;

use Candelabro\Enums\CandleColor;
use Candelabro\Services\Church;
use Candelabro\Traits\HasUserInput;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'candle:light', description: 'Light a candle')]
class LightCandle extends Command
{
    use HasUserInput;

    protected const CANDLE_COLORS = [
        'white' => 'White',
        'green' => 'Green',
        'orange' => 'Orange',
        'red' => 'Red',
        'purple' => 'Purple',
    ];

    protected const CANDLE_DAYS = [
        1 => '1 day',
        7 => '7 days',
    ];

    protected static function getParams(): array
    {
        return [
            'name' => [
                'description' => 'Your first name.',
                'label' => 'First name',
                'persist' => true,
            ],

            'lastname' => [
                'description' => 'Your last name.',
                'label' => 'Last name',
                'persist' => true,
            ],

            'city' => [
                'description' => 'Your city.',
                'label' => 'City',
                'persist' => true,
            ],

            'email' => [
                'description' => 'Your email address.',
                'label' => 'Email',
                'validate' => static::validateEmail(...),
                'persist' => true,
            ],

            'color' => [
                'description' => 'Which candle color you want to light.',
                'label' => 'Candle color',
                'options' => static::CANDLE_COLORS,
                'validate' => static::validateColor(...),
            ],

            'days' => [
                'description' => 'How many days you want to light the candle.',
                'label' => 'Days',
                'options' => static::CANDLE_DAYS,
                'validate' => static::validateDays(...),
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

    protected static function validateDays(string $days): ?string
    {
        if (!is_numeric($days)) {
            return 'Days must be a number.';
        }

        if ($days !== '1' && $days !== '7') {
            return 'Invalid number of days, must be 1 or 7.';
        }

        return null;
    }

    protected static function validateColor(string $color): ?string
    {
        if (!array_key_exists($color, static::CANDLE_COLORS)) {
            return 'Invalid candle color, must be one of: ' . implode(', ', array_keys(static::CANDLE_COLORS));
        }

        return null;
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to light a candle.');

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

        $this->persistUserInput($input);

        $candleColor = CandleColor::from($input['color']);

        (new Church())
            ->prayFor(
                firstName: $input['name'],
                lastName: $input['lastname'],
                city: $input['city'],
                email: $input['email'],
                color: $candleColor,
                days: (int) $input['days'],
            );

        $output->writeln('<info>Candle lit successfully! Your prayer has been heard.</info>');
        $output->writeln("You can see your candle at: {$candleColor->getListUrl()}");

        return Command::SUCCESS;
    }
}
