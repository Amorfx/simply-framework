<?php

namespace Simply\Tests\Query;

use Brain\Monkey;
use Simply\Core\Query\SimplyQuery;
use Simply\Tests\SimplyTestCase;

class SimplyQueryTest extends SimplyTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // The current object is memoized statically for the request
        (new \ReflectionProperty(SimplyQuery::class, 'currentObject'))->setValue(null, null);
    }

    public function testNoQueriedObjectReturnsFalse(): void
    {
        Monkey\Functions\when('get_queried_object')->justReturn(null);

        $this->assertFalse(SimplyQuery::getCurrentObject());
    }

    public function testUnsupportedQueriedObjectReturnsFalse(): void
    {
        // e.g. WP_Post_Type on a post type archive
        Monkey\Functions\when('get_queried_object')->justReturn(new \stdClass());

        $this->assertFalse(SimplyQuery::getCurrentObject());
    }
}
