import Link from 'next/link';
import React from 'react';

const SettingsDropdown = () => {
    return (
        <>
            <div className="dropdown filter-user">
                <button className="btn filter-user-btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Edit Profile
                </button>
                <ul className="dropdown-menu">
                    <li><Link className="dropdown-item" href="/instructor-settings">Instructor
                        Settings</Link></li>
                    <li><Link className="dropdown-item" href="/instructor-change-password">Change
                        Password</Link></li>
                    <li><Link className="dropdown-item" href="/instructor-social-profile">Social
                        Profile</Link></li>
                    <li><Link className="dropdown-item" href="/instructor-upload-photo">Upload
                        Photo</Link></li>
                </ul>
            </div>
        </>
    );
};

export default SettingsDropdown;