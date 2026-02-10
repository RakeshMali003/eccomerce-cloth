<?php
namespace Core;

class Queue
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Add job to queue
    public function push($jobName, $payload)
    {
        $stmt = $this->db->prepare("INSERT INTO jobs (job_name, payload, status, created_at) VALUES (?, ?, 'pending', NOW())");
        return $stmt->execute([$jobName, json_encode($payload)]);
    }

    // Process next job (Run this via Cron/Worker script)
    public function work()
    {
        // Lock the job row to prevent double processing
        $this->db->beginTransaction();

        $stmt = $this->db->query("SELECT id, job_name, payload FROM jobs WHERE status = 'pending' ORDER BY created_at ASC LIMIT 1 FOR UPDATE SKIP LOCKED");
        $job = $stmt->fetch();

        if ($job) {
            // Mark as processing
            $upd = $this->db->prepare("UPDATE jobs SET status = 'processing' WHERE id = ?");
            $upd->execute([$job['id']]);
            $this->db->commit();

            // Execute Logic (In real app, map job_name to Class)
            try {
                $this->process($job);

                // Mark complete
                $fin = $this->db->prepare("UPDATE jobs SET status = 'completed', completed_at = NOW() WHERE id = ?");
                $fin->execute([$job['id']]);
                echo "Processed Job: {$job['id']}\n";
            } catch (\Exception $e) {
                // Fail
                $fail = $this->db->prepare("UPDATE jobs SET status = 'failed' WHERE id = ?");
                $fail->execute([$job['id']]);
                echo "Failed Job: {$job['id']}\n";
            }
        } else {
            $this->db->commit();
            // echo "No jobs\n";
        }
    }

    private function process($job)
    {
        $data = json_decode($job['payload'], true);
        if ($job['job_name'] === 'send_email') {
            // mail($data['to'], $data['subject'], $data['body']);
            // Simulation
            sleep(1);
        }
    }
}
?>