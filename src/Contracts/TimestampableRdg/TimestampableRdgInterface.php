<?php                                      
                                                     
namespace App\Contracts\TimestampableRdg;

interface TimestampableRdgInterface
{
    public function getCreatedAt(): \DateTimeInterface;

    public function setCreatedAt(\DateTimeInterface $createdAt);

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(\DateTimeInterface $updatedAt);
}
