<?php

namespace DMT\Ubl\Generate;

class ElementSchema
{
    public function __construct(
        public ComponentSchema $component,
        public string $minOccurs,
        public string $maxOccurs,
    ) {
    }

    public function isIgnored(): bool
    {
        return $this->component->isIgnored();
    }

    public function build(BuildContext $ctx): void
    {
        $this->component->build(
            $ctx,
            $this->minOccurs == "0",
            $this->maxOccurs != "1",
        );
    }
}
