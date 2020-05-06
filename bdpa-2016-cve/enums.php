<?php
    // Define some enums
    abstract class LoginStatus
    {
        const Admin = 'AD';
        const Banned = 'BN';
        const User = 'UR';
        const UserNeverLoggedIn = 'UX';
    }

    abstract class CVEStatus
    {
        const Approved = 'AP';
        const Denied = 'DN';
        const Pending = 'PD';
    }

    abstract class CVESeverity
    {
        const High = 'H';
        const Medium = 'M';
        const Low = 'L';
    }

    abstract class AuthFailureType
    {
        const None = 0;
        const BadCredentials = 1;
        const DoesNotExist = 2;
        const Banned = 4;
        const Locked = 5;
        const MissingUsername = 16;
        const MissingPassword = 32;
        const PasswordNoMatch = 64;
        const PasswordInsecure = 128;
    }

    abstract class Entities
    {
        const None = 0;
        const UserByAdmin = 1;
        const UserByRegistration = 2;
        const CVENewOrEdit = 3;
    }

    abstract class SystemMessages
    {
        const SignupsEnabled = 'Signups are currently enabled';
        const SignupsDisabled = 'Signups are currently disabled';
    }
