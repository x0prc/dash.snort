<?php

use PHPUnit\Framework\TestCase;

class ReportTest extends TestCase
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
            $this->fail('Database connection failed: ' . $this->db->connect_error);
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS event (
                sid int(10) unsigned NOT NULL,
                cid int(10) unsigned NOT NULL,
                timestamp datetime NOT NULL,
                signature varchar(255),
                src_ip varchar(15),
                dst_ip varchar(15),
                PRIMARY KEY (sid, cid)
            )
        ");

        $this->db->query("DELETE FROM event");
    }

    protected function tearDown(): void
    {
        if ($this->db) {
            $this->db->close();
        }
    }

    private function seedEvents(): void
    {
        $events = [
            [1, 1, 'TCP Flood', '192.168.1.10', '10.0.0.1'],
            [1, 2, 'TCP Flood', '192.168.1.11', '10.0.0.1'],
            [1, 3, 'Port Scan', '192.168.1.12', '10.0.0.2'],
            [1, 4, 'Malware',   '192.168.1.13', '10.0.0.3'],
            [1, 5, 'Port Scan', '192.168.1.14', '10.0.0.4'],
        ];

        $stmt = $this->db->prepare("
            INSERT INTO event (sid, cid, timestamp, signature, src_ip, dst_ip)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($events as $idx => $e) {
            [$sid, $cid, $signature, $src_ip, $dst_ip] = $e;
            $timestamp = date('Y-m-d H:i:s', time() + $idx);

            $stmt->bind_param(
                'iissss',
                $sid,
                $cid,
                $timestamp,
                $signature,
                $src_ip,
                $dst_ip
            );
            $ok = $stmt->execute();
            $this->assertTrue($ok, 'Failed seeding event row for report test');
        }

        $stmt->close();
    }

    public function testReportAggregationQuery()
    {
        $this->seedEvents();

        $sql = "
            SELECT signature, COUNT(*) AS cnt
            FROM event
            GROUP BY signature
            ORDER BY cnt DESC
        ";

        $result = $this->db->query($sql);
        $this->assertNotFalse($result, 'Aggregation query failed');

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[$row['signature']] = (int) $row['cnt'];
        }

        // Assert counts match seeded data
        $this->assertArrayHasKey('TCP Flood', $data);
        $this->assertArrayHasKey('Port Scan', $data);
        $this->assertArrayHasKey('Malware', $data);

        $this->assertSame(2, $data['TCP Flood']);
        $this->assertSame(2, $data['Port Scan']);
        $this->assertSame(1, $data['Malware']);
    }

    public function testReportDataFormatForPlot()
    {
        $this->seedEvents();

        $sql = "
            SELECT signature, COUNT(*) AS cnt
            FROM event
            GROUP BY signature
            ORDER BY cnt DESC
        ";

        $result = $this->db->query($sql);
        $this->assertNotFalse($result, 'Aggregation query failed');

        $plotData = [];
        while ($row = $result->fetch_assoc()) {
            $plotData[] = [$row['signature'], (int) $row['cnt']];
        }

        // Basic structure checks
        $this->assertIsArray($plotData);
        $this->assertNotEmpty($plotData);

        foreach ($plotData as $entry) {
            $this->assertIsArray($entry);
            $this->assertCount(2, $entry);
            $this->assertIsString($entry[0]); 
            $this->assertIsInt($entry[1]);    
        }
    }

    public function testEmptyDatabaseFallback()
    {
        // No seeding here – table is empty by setUp()

        $sql = "
            SELECT signature, COUNT(*) AS cnt
            FROM event
            GROUP BY signature
            ORDER BY cnt DESC
        ";

        $result = $this->db->query($sql);
        $this->assertNotFalse($result, 'Aggregation query failed on empty DB');
        $this->assertSame(0, $result->num_rows, 'Expected no rows for empty DB');
    }
}
