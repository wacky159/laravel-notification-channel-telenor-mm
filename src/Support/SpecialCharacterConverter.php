<?php

declare(strict_types=1);

namespace Wacky159\TelenorMM\Support;

class SpecialCharacterConverter
{
    use HasSpecialCharacterConversion;

    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * Set custom special character mapping
     *
     * @param array<string, string> $mapping
     * @return self
     */
    public function map(array $mapping): self
    {
        $this->setSpecialCharacterMapping($mapping);
        return $this;
    }

    /**
     * Convert the content using the current mapping
     *
     * @return string
     */
    public function convert(): string
    {
        return $this->convertSpecialCharacters($this->content);
    }

    /**
     * Convert the content using the current mapping
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->convert();
    }
}
