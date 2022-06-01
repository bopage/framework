<?php
namespace App\Shop\Entity;

use Framework\Entity\TimeStamp;

class Product
{

    
    /**
     * id
     *
     * @var int|null
     */
    private $id;
    
    /**
     * name
     *
     * @var string|null
     */
    private $name;
    
    /**
     * slug
     *
     * @var string|null
     */
    private $slug;
    
    /**
     * description
     *
     * @var string|null
     */
    private $description;
    
    /**
     * price
     *
     * @var float|null
     */
    private $price;
    
    /**
     * image
     *
     * @var string|null
     */
    private $image;

    use TimeStamp;

    /**
     * Get id
     *
     * @return  int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param  int|null  $id  id
     *
     * @return  self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get name
     *
     * @return  string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param  string|null  $name  name
     *
     * @return  self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get slug
     *
     * @return  string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param  string|null  $slug  slug
     *
     * @return  self
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get description
     *
     * @return  string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param  string|null  $description  description
     *
     * @return  self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get price
     *
     * @return  float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Set price
     *
     * @param  float|null|string  $price  price
     *
     * @return  self
     */
    public function setPrice($price): self
    {
        if (is_string($price)) {
            $price = floatval($price);
        }
        $this->price = $price;

        return $this;
    }

    /**
     * Get image
     *
     * @return  string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param  string|null  $image  image
     *
     * @return  self
     */
    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getThumb()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/products/' . $filename . '_thumb.' . $extension;
    }

    public function getImageUrl()
    {
        return '/uploads/products/' . $this->image;
    }

    public function getPdf()
    {
        return "$this->id.pdf";
    }
}
