# Snooze
Event-driven thread notification management library for code using the pthreads extension

## Use cases
This library allows a parent thread to conveniently process notifications from an array of child threads, firing callbacks when a notification is received.

## Example
```php
class SleepyThread extends \Thread{
	private $notifier;

	public function __construct(\pocketmine\snooze\SleeperNotifier $notifier){
		$this->notifier = $notifier;
	}

	public function run() : void{
		while(true){
			//do some work
			sleep(5);

			//send a notification to the main thread
			//the parent thread doesn't have to be sleeping to receive this, it'll process it next time it tries to go 
			//back to sleep
			//if the parent thread is sleeping, it'll be woken up to process notifications immediately.
			$this->notifier->wakeupSleeper();
		}
	}
}

$sleeper = new \pocketmine\snooze\SleeperHandler();

$sleeper->addNotifier($notifier = new \pocketmine\snooze\SleeperNotifier(), function() : void{
	//do some things when this notifier sends a notification
	echo "Sleepy thread woke us up!\n";
});

$thread = new SleepyThread($notifier);
$thread->start();

while(true){
	$start = microtime(true);
	//do some work that you do every tick

	//process any pending notifications, then try to sleep 50ms until the next tick
	//this may wakeup at any time to process received notifications
	//if it wakes up and there is still time left to sleep before the specified time, it will go back to sleep again
	//until that time, guaranteeing a delay of at least this amount
	//if there are notifications waiting when this is called, they'll be processed before going to sleep
	$sleeper->sleepUntil($start + 0.05);
}

while(true){
	//alternatively, if you want to only wait for notifications and not tick:
	//but from the pthreads rulebook, only ever wait FOR something!
	//this will wait indefinitely until something wakes it up, and then return immediately
	$sleeper->sleepUntilNotification();
}
```
