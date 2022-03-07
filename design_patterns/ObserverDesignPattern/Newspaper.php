<?php

class NewsPaper implements \SplSubject
{
    private string $name;
    private array $observers = [];
    private string $content;

    public function __construct($name) {
        $this->name = $name;
    }

    //add observer
    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;
    }

    //remove observer
    public function detach(SplObserver $observer)
    {
        $key = array_search($observer, $this->observers, true);
        if ($key) {
            unset($this->observers[$key]);
        }
    }

    //notify observers(or some of them)
    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    //set breakouts news
    public function breakOutNews($content) {
        $this->content = $content;
        $this->notify();
    }

    public function getContent() : string {
        return $this->content . " ({$this->name}) ";
    }
}

class Reader implements SplObserver {

    private string $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function update(SplSubject $subject) {
        echo $this->name . ' is reading breakout news <b> ' . $subject->getContent() . '</b><br/>';
    }
}

$newspaper = new NewsPaper("Daily Star");

$iman = new Reader("Iman Ali");
$ishak = new Reader("Ishak Ahmed");
$rashid = new Reader("Abdur Rashid");

//add reader
$newspaper->attach($iman);
$newspaper->attach($ishak);
$newspaper->attach($rashid);

//remove reader
$newspaper->detach($rashid);

//Set break outs
$newspaper->breakOutNews("Bangladesh break down!");