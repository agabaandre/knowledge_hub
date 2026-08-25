"use client";
import React, { useState } from "react";
import UserSettingsDropdown from "./UserSettingsDropdown";

const StudentUploadPhotoMain = () => {
  // Default image state
  const [image, setImage] = useState<string>("/assets/images/blog/blog-thumb-01.webp");

  // Handle Image Change
  const handleImageChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setImage(reader.result as string);
      };
      reader.readAsDataURL(file);
    }
  };

  return (
    <div className="col-xl-9 col-lg-9 col-md-8">
      <div className="bd-dashboard-inner">
        <div className="bd-dashboard-title-inner">
          <div className="d-flex justify-content-between flex-wrap">
            <h4 className="bd-dashboard-title">Change Photo</h4>
            <UserSettingsDropdown />
          </div>
        </div>

        <div className="bd-profile-update-area">
          <div className="bd-cover-details-thumb details-slide-full mb-30">
            <div className="bd-cover-thumb-chnage">
              <div className="bd-cover-thumb-preview">
                <div
                  className="bd-cover-thumb-preview-box"
                  id="imagePreview"
                  style={{
                    backgroundImage: `url(${image})`
                  }}
                />
              </div>

              <div className="bd-cover-thumb-edit">
                <input
                  type="file"
                  id="imageUpload"
                  accept="image/*"
                  onChange={handleImageChange}
                />
                <label htmlFor="imageUpload">Add / Change Image</label>
              </div>
            </div>
          </div>

          <div className="bd-change-btn">
            <button className="bd-btn btn-primary">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default StudentUploadPhotoMain;
