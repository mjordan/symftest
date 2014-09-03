<?php

/*
 * Copyright (C) 2014 mjoyce
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace LOM\UserBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Form model for an administrative password change.
 */
class AdminChangePassword {

    /**
     * @Assert\Length(
     *     min = 6,
     *     minMessage = "Password should by at least 6 chars long"
     * )
     */
    protected $newPassword;

    /**
     * Set the new password
     *
     * @param string $newPassword unhashed password
     */
    public function setNewPassword($newPassword) {
        $this->newPassword = $newPassword;
    }

    /**
     * Get the unhashed password.
     *
     * @return string
     */
    public function getNewPassword() {
        return $this->newPassword;
    }

}
