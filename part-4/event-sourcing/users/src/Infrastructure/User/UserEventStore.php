<?php

declare(strict_types=1);

namespace Users\Infrastructure\User;

use Amp\Promise;
use Framework\Service\AbstractEventStore;
use Prooph\EventStore\Projections\State;
use Users\Domain\User\Events\UserEventInterface;
use Users\Domain\User\User;
use Users\Domain\User\UserStoreInterface;

class UserEventStore extends AbstractEventStore implements UserStoreInterface
{

    /**
     * @param  User           $user
     * @param  callable|null  $callback
     * @param  int            $start
     * @param  int            $count
     *
     * @return Promise
     */
    public function read(User $user, callable $callback = null, int $start = 0, int $count = 30): Promise
    {
        return $this->getEventStore()->readAndApply(
            $this->getStreamNameForUserId($user->getId()),
            $user,
            $start,
            $count,
            $callback
        );
    }

    /**
     * Saves the specified user
     *
     * @param  UserEventInterface  $event
     *
     * @return Promise
     */
    public function write(UserEventInterface $event): Promise
    {
        return $this->getEventStore()->write(
            $this->getStreamNameForUserId($event->getUserId()),
            $event
        );
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function hasStreamForUser(User $user): bool
    {
        return $this->getEventStore()->hasStream(
            $this->getStreamNameForUserId($user->getId())
        );
    }

    /**
     * @param  User           $user
     * @param  callable|null  $callback
     *
     * @return $this
     */
    public function queryEmailChanges(User $user, callable $callback = null): self
    {
        $class = \addslashes(\Users\Domain\User\Events\EmailChangedEvent::class);
        $streamName = $this->getStreamNameForUserId($user->getId());
        $this->retrieveQueryStateOrCreate(
            "user-{$user->getId()}-email-changed",
            <<<JS
fromStream('{$streamName}')
.when({
	\$init: function() {
		return {
			count: 0,
			emails: []
		}
	},
	"{$class}": function(state, event) {
		state.count += 1;
		state.emails.push(event.data.email);
	}
})
JS,
            function (State $state) use ($callback) {
                $payload = $state->payload();
                echo 'User changed the email ' . ((int) $payload['count'] ?? 0) . ' time(s):' . PHP_EOL;
                var_dump($payload['emails']);
                if ($callback !== null) {
                    $callback();
                }
            }
        );
        return $this;
    }

    /**
     * @param string $userId
     *
     * @return string
     */
    protected function getStreamNameForUserId(string $userId): string
    {
        return "user-{$userId}";
    }
}
