import Link from 'next/link';
import React from 'react';

const UserSettingsDropdown = () => {
    return (
        <>
            <div className="dropdown filter-user">
                <button
                    className="btn filter-user-btn btn-secondary dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    Edit Profile
                </button>
                <ul className="dropdown-menu">
                    <li>
                        <Link className="dropdown-item" href="/student-settings">
                            User Settings
                        </Link>
                    </li>
                    <li>
                        <Link className="dropdown-item" href="/student-change-password">
                            Change Password
                        </Link>
                    </li>
                    <li>
                        <Link className="dropdown-item" href="/student-social-profile">
                            Social Profile
                        </Link>
                    </li>
                    <li>
                        <Link className="dropdown-item" href="/student-upload-photo">
                            Upload Photo
                        </Link>
                    </li>
                </ul>
            </div>
        </>
    );
};

export default UserSettingsDropdown;