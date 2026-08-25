import React from 'react';

const profileDetails = [
  { field: "Registration Date", value: "December 25, 2024 12:30pm" },
  { field: "First Name", value: "Chamain" },
  { field: "Last Name", value: "Louis" },
  { field: "Username", value: "Instructor" },
  { field: "Email", value: "info@gamil.com" },
  { field: "Phone Number", value: "+967 019 2425 990" },
  { field: "Occupation/Skill", value: "Application Developer" },
  {
    field: "Biography",
    value: "I'm Jack Dawson, a coding enthusiast and Computer Science graduate. With hands-on experience in software development, I now teach to make coding accessible and engaging. My goal is to simplify complexities and help students appreciate the art and logic of coding."
  }
];

const InstructorProfileMain = () => {
  return (
      <div className="col-xl-9 col-lg-9 col-md-8">
        <div className="bd-dashboard-inner">
          <div className="bd-dashboard-title-inner">
            <h4 className="bd-dashboard-title">My Profile</h4>
          </div>
          <div className="bd-dashboard-profile-info table-responsive">
            <table className="table table-bordered table-head-bg">
              <thead>
                <tr>
                  <th style={{ width: '200px' }}>Field</th>
                  <th>Details</th>
                </tr>
              </thead>
              <tbody>
                {profileDetails.map(({ field, value }) => (
                  <tr key={field}>
                    <td>{field}</td>
                    <td>{value}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
  );
};

export default InstructorProfileMain;
