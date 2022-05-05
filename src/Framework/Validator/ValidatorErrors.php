<?php

namespace Framework\Validator;

class ValidatorErrors
{
    private $key;

    private $rule;

    private $attributes;

    private $messages = [
        'required' => "Le champ %s est requis",
        'empty' => "Le champ ne peut pas être vide",
        'slug' => "Le champ %s n\'est pas un slug valide",
        'minLength' => "Le champ %s doit contenir plus de %d caractères",
        'maxLength' => "Le champ %s doit contenir mois de %d caractères",
        'betweenLength' => "Le champ %s doit contenir entre %d et %d carctères",
        'datetime' => "Le champ %s doit être un datetime valide (%d)",
        'exist' => "La categorie %s n'existe pas dans la table %s",
        'unique' => "La champ %s doit être unique"
    ];

    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    public function __toString()
    {
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return (string)call_user_func_array('sprintf', $params);
    }
}
