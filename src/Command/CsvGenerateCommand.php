<?php

namespace App\Command;

use Exception;
use Sepia\PoParser\Catalog\CatalogArray;
use Sepia\PoParser\Parser;
use Sepia\PoParser\SourceHandler\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class CsvGenerateCommand extends Command {
	protected static $defaultName = 'app:csv:generate';
	protected static $defaultDescription = 'Generate a csv file from a po file using gettext';

	protected function configure(): void
    {
        $this
            ->addArgument('po_file', InputArgument::REQUIRED, 'the po file to use to generate csv')
            ->addOption(
                'source-language',
                's',
                InputOption::VALUE_REQUIRED,
                'Override the source language of po file (msgid language)',
                'en_US'
            )
            ->addOption(
                'target-language',
                't',
                InputOption::VALUE_REQUIRED,
                'Override the target language (msgstr language) - default is red from "Language" po_file header'
            )
        ;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileHandler    = new FileSystem($input->getArgument('po_file'));
        $parser         = new Parser($fileHandler);
        $catalog        = $parser->parse();
        $sourceLanguage = $input->getOption('source-language');
        $targetLanguage = $input->getOption('target-language') ?: $this->getDefaultTargetLanguage($catalog);
        $array          = [];
        $array[]        = [$sourceLanguage, $targetLanguage];
        foreach ($catalog->getEntries() as $entry) {
            $array[] = [$entry->getMsgId(), $entry->getMsgStr()];
        }
        $context  = [CsvEncoder::NO_HEADERS_KEY => true];
        $encorder = new CsvEncoder($context);
        $encoded  = $encorder->encode($array, CsvEncoder::FORMAT);

        echo $encoded;


        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    private function getDefaultTargetLanguage(CatalogArray $catalog): ?string
    {
        $value = $this->getHeaderValue($catalog->getHeaders(), 'Language');
        if (!$value) {
            throw new Exception('Target "Language" header not found or empty, please specify a --target-language as option.');
        }

        return $value;
    }

    /**
     * @param array  $headers
     * @param string $headerName
     *
     * @return string|null
     */
    protected function getHeaderValue(array $headers, string $headerName): ?string
    {
        $header = array_values(
            array_filter(
                $headers,
                function ($string) use ($headerName) {
                    return preg_match('/' . $headerName . ':(.*)/', $string) == 1;
                }
            )
        );

        return count($header) ? trim(preg_replace('/' . $headerName . ':(.*)/', '$1', $header[0])) : null;
    }
}
