<?php

test('run the list command', function () {
    $this->artisan('list')->assertExitCode(0);
});
