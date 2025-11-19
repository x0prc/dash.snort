<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: 'root';
        $name = getenv('DB_NAME') ?: 'snort';

        $this->db = new mysqli($host, $user, $pass, $name);

        if ($this->db->connect_error) {
            $this->fail("Database connection failed: " . $this->db->connect_error);
        }
    }

    protected function tearDown(): void
    {
        if ($this->db) {
            $this->db->close();
        }
    }

    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(mysqli::class, $this->db);
        $this->assertEmpty($this->db->connect_error);
    }

    public function testEventTableExists()
    {
        $result = $this->db->query("SHOW TABLES LIKE 'event'");
        $this->assertTrue($result !== false && $result->num_rows === 1, "Table 'event' does not exist.");
    }

    public function testInsertEvent()
    {
        $sid = rand(100,1000);
        $cid = rand(100,1000);
        $timestamp = date('Y-m-d H:i:s');
        $signature = 'Test Alert';
        $src_ip = '192.168.1.1';
        $dst_ip = '192.168.1.2';

        $stmt = $this->db->prepare("INSERT INTO event (sid, cid, timestamp, signature, src_ip, dst_ip) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iissss', $sid, $cid, $timestamp, $signature, $src_ip, $dst_ip);
        $result = $stmt->execute();
        $stmt->close();

        $this->assertTrue($result, "Insert into 'event' table failed.");

        $result = $this->db->query("SELECT * FROM event WHERE sid = $sid AND cid = $cid");
        $this->assertTrue($result !== false && $result->num_rows === 1, "Inserted event row not found.");
    }

    public function testDeleteEvent()
    {
        $row = $this->db->query("SELECT sid, cid FROM event ORDER BY timestamp DESC LIMIT 1")->fetch_assoc();
        if ($row) {
            $stmt = $this->db->prepare("DELETE FROM event WHERE sid = ? AND cid = ?");
            $stmt->bind_param('ii', $row['sid'], $row['cid']);
            $result = $stmt->execute();
            $stmt->close();

            $this->assertTrue($result, "Event delete failed.");

            $result = $this->db->query("SELECT * FROM event WHERE sid = {$row['sid']} AND cid = {$row['cid']}");
            $this->assertTrue($result !== false && $result->num_rows === 0, "Deleted event row still exists.");
        } else {
            $this->markTestSkipped("No event row found to delete.");
        }
    }
}
