<?php

namespace METRIC\App;

interface ControllerInterface
{
  public function read(array $data);
  public function send(array $data);
  public function update(array $data);
}