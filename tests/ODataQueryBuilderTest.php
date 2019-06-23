<?php
//./vendor/bin/phpunit --bootstrap ./vendor/autoload.php ./tests/ODataQueryBuilderTest.php
// Be sure to run composer dump-autoload anytime different files are required.

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ODataQueryBuilder\ODataQueryBuilder;


final class ODataQueryBuilderTest extends TestCase {

    public function testFromAndFind() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');
        $query = $builder->from('People')->find('bob')->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People(\'bob\')', $query);
    }

    public function testComplex() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');
        $query = $builder->from('People')->find('bob')
        ->thenFrom('Trips')
        ->filterBuilder()
            ->openParentheses()
                ->where('Name')->contains('US')->or()->where('Name')->equals('TestName')
            ->closeParentheses()
            ->and()->where('Cost')->lessThan(3000)
        ->addToQuery()
        ->orderByBuilder()
            ->orderBy('Name')->ascending()->thenBy('Cost')->descending()
        ->addToQuery()
        ->encodeUrl(false)
        ->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People(\'bob\')/Trips?$filter=(contains(Name,\'US\') or Name eq \'TestName\') and Cost lt 3000&$orderby=Name asc, Cost desc',
            $query
        );
    }
}