<?php

namespace Core;

class AvatarGenerator
{
    private string $background;
    private string $color;
    private int $size;

    public function __construct(string $background = '', string $color = 'fff', int $size = 128)
    {
        $this->background = $background ?: $this->generateRandomColor();
        $this->color = $color;
        $this->size = $size;
    }

    public function generate(string $name): string
    {
        $encodedName = urlencode($name);
        return "https://ui-avatars.com/api/?name={$encodedName}&background={$this->background}&color={$this->color}&size={$this->size}";
    }

    private function generateRandomColor(): string
    {
        return sprintf('%02x%02x%02x', rand(0, 255), rand(0, 255), rand(0, 255));
    }
}
