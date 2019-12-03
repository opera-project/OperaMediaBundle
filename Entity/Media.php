<?php

namespace Opera\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Opera\MediaBundle\Validator\Constraints as MediaAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Opera\MediaBundle\Repository\MediaRepository")
 * @UniqueEntity("slug")
 */
class Media
{
    use TimestampableEntity;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mime;

    /**
     * @ORM\ManyToOne(targetEntity="Opera\MediaBundle\Entity\Folder", inversedBy="medias")
     * @ORM\JoinColumn(name="folder_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $folder;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @MediaAssert\IsMediaSource()
     */
    private $source;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getMime(): ?string
    {
        return $this->mime;
    }

    public function setMime(string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    public function setFolder(?Folder $folder): self
    {
        $this->folder = $folder;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;
        if ($this->getFolder() && $this->getFolder()->getSource() !== $this->source) {
            $this->getFolder()->removeMedia($this);
        }

        return $this;
    }

    public function getType(): string
    {
        return 'media';
    }

    public function __toString(): string
    {
        return 'Media '.$this->getSource().'.'.$this->getName();
    }

    public function getFolderPath(): string
    {
        if (!$this->getFolder()) {
            return '/';
        }

        return $this->getFolder()->getFolderPath();
    }

    public function getMediaPath(): string
    {
        return $this->getFolderPath() == '/' ? $this->getFolderPath().$this->getName():$this->getFolderPath().'/'.$this->getName();
    }

}
