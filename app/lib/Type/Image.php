<?php

namespace App\Lib\Type;

use Intervention\Image\Interfaces\ImageInterface;

class Image
{
    private string $uri;
    private string $width;
    private string $height;
    private string $mimeType;
    private string $exif;
    private string $title;
    private string $description;
    private bool $isValid = true;
    private ImageInterface $image;

    public function __construct(
        string $uri,
        string $width,
        string $height,
        string $mimeType,
        string $title,
        string $description,
        string $exif = "",
        ImageInterface $image = null
    ) {
        if ($uri === "null") {
            $this->isValid = false;
        }
        $this->uri = $uri;
        $this->width = $width;
        $this->height = $height;
        $this->mimeType = $mimeType;
        $this->exif = $exif;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
    }

    /**
     * @return ImageInterface
     */
    public function getImage(): ImageInterface
    {
        return $this->image;
    }

    /**
     * @param ImageInterface $image
     *
     * @return Image
     */
    public function setImage(ImageInterface $image): Image
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Image
     */
    public function setTitle(string $title): Image
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Image
     */
    public function setDescription(string $description): Image
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     *
     * @return Image
     */
    public function setUri(string $uri): Image
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     *
     * @return Image
     */
    public function setWidth(string $width): Image
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $height
     *
     * @return Image
     */
    public function setHeight(string $height): Image
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     *
     * @return Image
     */
    public function setMimeType(string $mimeType): Image
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExif()
    {
        return $this->exif;
    }

    /**
     * @param string $exif
     *
     * @return Image
     */
    public function setExif(string $exif): Image
    {
        $this->exif = $exif;
        return $this;
    }

}
