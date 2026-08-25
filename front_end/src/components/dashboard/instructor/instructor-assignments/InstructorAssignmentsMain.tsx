import { IInstructorAssignment } from '@/interFace/dashboard-interface';
import Link from 'next/link';
import React from 'react';

// Assignment data with proper types
const assignmentsData: IInstructorAssignment[] = [
  { id: '#01', user: 'Louis', quiz: 'About PHP', result: '80%', time: '10 Minutes', status: 'Pass', statusClass: 'success' },
  { id: '#02', user: 'Louis', quiz: 'About PHP', result: '80%', time: '10 Minutes', status: 'Fail', statusClass: 'danger' },
  { id: '#03', user: 'Louis', quiz: 'About PHP', result: '80%', time: '10 Minutes', status: 'Pass', statusClass: 'success' },
  { id: '#04', user: 'Louis', quiz: 'About PHP', result: '80%', time: '10 Minutes', status: 'Fail', statusClass: 'danger' },
];


// AssignmentRow component with typed props
const AssignmentRow: React.FC<IInstructorAssignment> = ({ id, user, quiz, result, time, status, statusClass }) => (
  <tr>
    <td><p>{id}</p></td>
    <td><p>{user}</p></td>
    <td><p>{quiz}</p></td>
    <td><p>{result}</p></td>
    <td><p>{time}</p></td>
    <td><div className={`bd-badge badge-${statusClass}`}>{status}</div></td>
    <td>
      <div className="bd-button-action">
        <Link className="bd-default-tooltip edit" href="#"><span><i className="fa-light fa-pen-to-square"></i></span></Link>
        <Link className="bd-default-tooltip view" href="#"><span><i className="fa-sharp fa-light fa-eye"></i></span></Link>
        <Link className="bd-default-tooltip delete" href="#"><span><i className="fa-light fa-trash-can"></i></span></Link>
      </div>
    </td>
  </tr>
);

const InstructorAssignmentsMain: React.FC = () => (
    <div className="col-xl-9 col-lg-9 col-md-8">
      <div className="bd-dashboard-inner">
        <div className="bd-dashboard-title-inner">
          <h4 className="bd-dashboard-title">My Assignments</h4>
        </div>
        <div className="bd-dashboard-table table-responsive mt-30">
          <table className="table table-bordered table-head-bg">
            <thead>
              <tr>
                <th>Id</th>
                <th>User</th>
                <th>Quiz</th>
                <th>Result</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              {assignmentsData.map((assignment) => (
                <AssignmentRow key={assignment.id} {...assignment} />
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
);

export default InstructorAssignmentsMain;
