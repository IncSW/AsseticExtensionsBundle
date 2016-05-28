'use strict';

import Foo from 'foo';

/**
 * Class Bar
 */
class Bar extends Foo {

    constructor() {
        super();
        this.fooBar = 'Hello, World!';
    }

    sayHello() {
        console.log(this.fooBar);
    }
}
