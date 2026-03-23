<?php

namespace DMT\Ubl\Generate;

use Generator;
use RuntimeException;
use ZipArchive;

class Schemas
{
    public readonly ZipArchive $zip;
    public array $components = [];
    /** @var array<string,DocumentSchema>  */
    public array $documents = [];

    public function __construct(
        public readonly string $file,
        public readonly string $version = '2.1'
    )
    {
        $this->zip = new ZipArchive();

        if ($this->zip->open($this->file) !== true) {
            throw new RuntimeException("Cannot open zip");
        }

        $componentNamespaces = [
            'cac' => 'CommonAggregateComponents',
            'cbc' => 'CommonBasicComponents',
        ];

        foreach ($componentNamespaces as $ns => $name) {
            $this->components[$ns] = iterator_to_array($this->generateComponents($ns, $name));
            ksort($this->components[$ns]);
        }

        $this->documents = iterator_to_array($this->generateDocuments());
        ksort($this->documents);
    }

    public function __destruct()
    {
        $this->zip->close();
    }

    private function generateComponents(string $ns, string $name): Generator
    {
        $i = $this->zip->locateName("xsd/common/UBL-$name-$this->version.xsd");
        $schema = simplexml_load_string($this->zip->getFromIndex($i));

        foreach ($schema->xpath('/xsd:schema/xsd:element') as $element) {
            $name = (string)$element->attributes()['name'];
            $type = (string)$element->attributes()['type'];

            $complexType = $schema->xpath("/xsd:schema/xsd:complexType[@name='$type']")[0];

            yield $name => new ComponentSchema(
                $ns,
                $element,
                $complexType
            );
        }
    }

    private function generateDocuments(): Generator
    {
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $stat = $this->zip->statIndex($i);
            $name = $stat['name'];

            if (!fnmatch('xsd/maindoc/*.xsd', $stat['name'])) {
                continue;
            }

            $document = simplexml_load_string($this->zip->getFromIndex($i));

            if (!$document) {
                throw new RuntimeException("Cannot open document $name");
            }

            $documentSchema = new DocumentSchema($this, $document);

            yield $documentSchema->getRootElementName() => $documentSchema;
        }
    }
}
