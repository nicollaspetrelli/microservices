<?php

declare(strict_types=1);

namespace Users\Domain\User;

interface UserRepositoryInterface {

  /**
   * Saves the specified user
   *
   * @param User $user
   */
  public function persist(User $user): void;

}
