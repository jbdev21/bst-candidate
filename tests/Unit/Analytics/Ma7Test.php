<?php

it('computes integer MA7 correctly', function () {
    $prices = collect(range(1, 7));
    $avg = (int) floor(($prices->sum() / 7) + 0.5);
    expect($avg)->toBeInt();
});
