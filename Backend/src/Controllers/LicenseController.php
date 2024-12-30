<?php
namespace App\Controllers;

use App\Models\License;
use App\Models\User;

class LicenseController
{
    private $license;
    private $user;

    public function __construct($db)
    {
        $this->license = new License($db);
        $this->user = new User($db);
    }

    public function generate($email, $applicationId)
    {
        // Check if the user is active
        $user = $this->user->findByEmail($email);
        if (!$user || $user['activation_status'] !== 'active') {
            return json_encode(['message' => 'Only active users can generate a license']);
        }

        $licenseCode = $this->license->generate($email, $applicationId);
        return json_encode(['license' => $licenseCode, 'message' => 'License generated successfully']);
    }

    public function validate($licenseCode, $applicationId)
    {
        $isValid = $this->license->validate($licenseCode, $applicationId);
        return json_encode(['valid' => $isValid]);
    }
}
?>