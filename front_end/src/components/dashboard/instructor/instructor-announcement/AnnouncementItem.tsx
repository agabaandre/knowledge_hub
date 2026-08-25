import React from 'react';
import { FC } from 'react';
import AnnouncementsSvg from '@/svg/AnnouncementsSvg';
import { IAnnouncement } from '@/interFace/dashboard-interface';

const AnnouncementItem: FC<IAnnouncement> = ({ title, date, time, description }) => {
  return (
    <div className="bd-dashboard-announcements-item mb-30">
      <div className="inner">
        <div className="icon">
          <AnnouncementsSvg />
        </div>
        <div className="content">
          <h5 className="title">{title}</h5>
          <ul className="bd-meta">
            <li className="has-separator">
              <div className="meta-icon">
                <i className="fa-light fa-calendar"></i>
              </div>
              <span>{date}</span>
            </li>
            <li>
              <div className="meta-icon">
                <i className="fa-light fa-clock"></i>
              </div>
              <span>{time}</span>
            </li>
          </ul>
          <p>{description}</p>
        </div>
      </div>
    </div>
  );
};

export default AnnouncementItem;
