<?php

namespace Framework\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

    /**
     * Génère le code html d'un champ
     *
     * @param  array $context
     * @param  string $key
     * @param  mixed $value
     * @param  string $label
     * @param  array $option
     * @return string
     */
    public function field(array $context, string $key, $value, ?string $label = null, array $option = []): string
    {
        $type = $option['type'] ?? 'text';
        $error = $this->getErrors($context, $key);
        $value = $this->convertValue($value);
        $attributes = [
            'class' => trim('form-control ' . ($option['class'] ?? '')),
            'name' => $key,
            'id' => $key
        ];
        if ($error) {
            $attributes['class'] .= ' is-invalid';
        }
        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } elseif ($type === 'file') {
            $input = $this->file($attributes);
        } elseif (array_key_exists('options', $option)) {
            $input = $this->select($value, $option['options'], $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }
        return "<div class='mb-3'>
            <label for='{$key}' class='form-label'>{$label}</label>
            {$input}
            {$error}</div>";
    }

    /**
     * Génère l'html en fonction des erreurs du contexte
     *
     * @param  array $context
     * @param  string $key
     * @return void
     */
    private function getErrors(array $context, string $key)
    {
        $error = $context['errors'][$key] ?? false;
        if ($error) {
            return  "<div class='invalid-feedback'>{$error}</div>";
        } else {
            return '';
        }
    }

    /**
     * Génère le <textarea>
     *
     * @param  string|null $value
     * @param  string[] $attributes
     * @return string
     */
    private function textarea(?string $value = null, array $attributes): string
    {
        return "<textarea type='text' " . $this->getHtmlFromArray($attributes) . " rows='5'>{$value}</textarea>";
    }

    /**
     * Génère l'<input>
     *
     * @param  string $value
     * @param  string[] $attributes
     * @return string
     */
    private function input(?string $value = null, array $attributes): string
    {
        return "<input type='text' " . $this->getHtmlFromArray($attributes) . " value='{$value}'>";
    }

     /**
     * Génère l'<input>
     *
     * @param  string[] $attributes
     * @return string
     */
    private function file(array $attributes): string
    {
        return "<input type='file' " . $this->getHtmlFromArray($attributes) . " >";
    }

    /**
     * Génère <select>
     *
     * @param  string|null $value
     * @param  array $option
     * @param  array $attributes
     * @return string
     */
    private function select(?string $value = null, array $options, array $attributes): string
    {
        $htmlOption = array_reduce(array_keys($options), function (string $html, string $key) use ($value, $options) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $options[$key] . '</option>';
        }, "");

        return "<select " . $this->getHtmlFromArray($attributes) . ">
            $htmlOption
            </select>";
    }

    /**
     * Génère les attributs
     *
     * @param string[] $attributes
     */
    private function getHtmlFromArray(array $attributes): string
    {
        $htmlPart = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlPart[] = (string)$key;
            } elseif ($value !== false) {
                $htmlPart[] = "$key='$value'";
            }
        }
        return implode(' ', $htmlPart);
    }

    private function convertValue($value)
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return htmlentities($value);
    }
}
