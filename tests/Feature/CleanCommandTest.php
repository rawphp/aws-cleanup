<?php

test('run the clean command', function () {
    $this->artisan('clean')->assertExitCode(0);
});
