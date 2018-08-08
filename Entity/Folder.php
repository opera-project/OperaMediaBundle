<?php

namespace Opera\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="Opera\MediaBundle\Repository\FolderRepository")
 */
class Folder
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="Opera\MediaBundle\Entity\Media", mappedBy="folder")
     */
    private $medias;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $source;

    /**
     * @ORM\OneToMany(targetEntity="Opera\MediaBundle\Entity\Folder", mappedBy="parent")
     */
    private $childs;

    /**
     * @ORM\ManyToOne(targetEntity="Opera\MediaBundle\Entity\Folder", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    public function __construct()
    {
        $this->folders = new ArrayCollection();
        $this->medias = new ArrayCollection();
        $this->childs = new ArrayCollection();
    }

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

    /**
     * @return Collection|Media[]
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
            $media->setFolder($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->medias->contains($media)) {
            $this->medias->removeElement($media);
            // set the owning side to null (unless already changed)
            if ($media->getFolder() === $this) {
                $media->setFolder(null);
            }
        }

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;
        if ($this->getParent() && $this->getParent()->getSource() !== $this->source) {
            $this->getParent()->removeChild($this);
        }
    
        return $this;
    }

    /**
     * @return Collection|Folder[]
     */
    public function getChilds(): Collection
    {
        return $this->childs;
    }

    public function addChild(Folder $Child): self
    {
        if (!$this->childs->contains($Child)) {
            $this->childs[] = $Child;
            $Child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Folder $Child): self
    {
        if ($this->childs->contains($Child)) {
            $this->childs->removeElement($Child);
            // set the owning side to null (unless already changed)
            if ($Child->getParent() === $this) {
                $Child->setParent(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        if ($parent == null || ($this->getSource() === $parent->getSource())) {
            $this->parent = $parent;
        }

        return $this;
    }

    public function getType(): string
    {
        return 'folder';
    }

    public function __toString(): string
    {
        return 'Folder '.$this->getSource().'.'.$this->getName();
    }
}
