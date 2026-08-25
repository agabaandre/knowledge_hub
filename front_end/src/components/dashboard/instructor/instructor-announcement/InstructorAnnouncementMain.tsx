import React from 'react';
import AnnouncementItem from './AnnouncementItem';

const announcements = [
  {
    title: 'Full Stack Web Development with MERN',
    date: '11 Jan 2025',
    time: '10.30am',
    description:
      "Unleash your creativity with our Digital Marketing Mastery Program, where innovation meets expertise. Join now to elevate your skills and redefine what's possible.",
  },
  {
    title: 'Digital Marketing Mastery SEO & Social Media',
    date: '12 Jan 2025',
    time: '10.30am',
    description:
      'Become a digital marketing powerhouse! Enroll in our comprehensive [Course Name] and discover the strategies that drive online success. Your journey to digital mastery begins here.',
  },
  {
    title: 'Unlocking Insights with Data Analysis',
    date: '13 Jan 2025',
    time: '10.30am',
    description:
      'Social media awaits your mastery! Join our [Course Name] Workshop to discover the strategies that captivate audiences. Enroll now and become a social media influencer!',
  },
];

const InstructorAnnouncementMain: React.FC = () => {
  return (
      <div className="col-xl-9 col-lg-9 col-md-8">
        <div className="bd-dashboard-inner">
          <div className="bd-dashboard-title-inner">
            <h4 className="bd-dashboard-title">Announcements</h4>
          </div>
          <div className="bd-dashboard-announcements-wrapper">
            {announcements.map((announcement, index) => (
              <AnnouncementItem key={index} {...announcement} />
            ))}
          </div>
        </div>
      </div>
  );
};

export default InstructorAnnouncementMain;
