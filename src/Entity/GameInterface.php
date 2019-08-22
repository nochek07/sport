<?php

namespace App\Entity;

interface GameInterface
{
    public function getLeague(): ?League;

    public function setLeague(?League $league);

    public function getTeam1(): ?Team;

    public function setTeam1(?Team $team1);

    public function getTeam2(): ?Team;

    public function setTeam2(?Team $team2);

    public function getDate(): ?\DateTimeInterface;

    public function setDate(\DateTimeInterface $date);

    public function getLanguage(): ?Language;

    public function setLanguage(?Language $language);
}