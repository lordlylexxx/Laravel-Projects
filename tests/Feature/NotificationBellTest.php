<?php

it('redirects guests away from the notifications json endpoint on web', function () {
    $response = $this->get('/notifications');

    $response->assertRedirect();
});
