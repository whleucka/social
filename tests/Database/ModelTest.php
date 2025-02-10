<?php declare(strict_types=1);

namespace Tests\Routing;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testModelQuery()
    {
        // Test operands
        $sql = User::where("email", "test@test.com")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email = ?)", $sql["query"]);

        $sql = User::where("email", "!=", "test@test.com")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email != ?)", $sql["query"]);

        $sql = User::where("email", "<", "test@test.com")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email < ?)", $sql["query"]);

        $sql = User::where("email", "<=", "test@test.com")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email <= ?)", $sql["query"]);

        $sql = User::where("email", ">", "test@test.com")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email > ?)", $sql["query"]);

        $sql = User::where("email", ">=", "test@test.com")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email >= ?)", $sql["query"]);

        $sql = User::where("email", "IS", "test@test.com")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email IS ?)", $sql["query"]);

        $sql = User::where("email", "NOT", "test@test.com")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email NOT ?)", $sql["query"]);

        $sql = User::where("email", "LIKE", "%test@test.com%")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email LIKE ?)", $sql["query"]);

        // Order by
        $sql = User::where("first_name", "Will")->orderBy("first_name", "DESC")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (first_name = ?) ORDER BY first_name DESC", $sql["query"]);

        // Test chains
        $sql = User::where("email", "test@test.com")->andWhere("first_name", "Will")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email = ?) AND (first_name = ?)", $sql["query"]);

        $sql = User::where("email", "test@test.com")->andWhere("first_name", "Will")->andWhere("surname", "Hleucka")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email = ?) AND (first_name = ?) AND (surname = ?)", $sql["query"]);

        $sql = User::where("email", "test@test.com")->andWhere("first_name", "Will")->orWhere("surname", "Hleucka")->sql();
        $this->assertSame("SELECT * FROM `users` WHERE (email = ?) AND (first_name = ?) OR (surname = ?)", $sql["query"]);

    }
}
