import { IStudentAssignment } from '@/interFace/dashboard-interface';
import Link from 'next/link';
import React from 'react';

const assignments: IStudentAssignment[] = [
    {
        id: '#01',
        user: 'Louis',
        quiz: 'About PHP',
        result: '80%',
        time: '10 Minutes',
        status: 'Pass',
    },
    {
        id: '#02',
        user: 'Louis',
        quiz: 'About PHP',
        result: '80%',
        time: '10 Minutes',
        status: 'Fail',
    },
    {
        id: '#03',
        user: 'Louis',
        quiz: 'About PHP',
        result: '80%',
        time: '10 Minutes',
        status: 'Pass',
    },
    {
        id: '#04',
        user: 'Louis',
        quiz: 'About PHP',
        result: '80%',
        time: '10 Minutes',
        status: 'Fail',
    },
];

type Status = 'Pass' | 'Fail';

const badgeClasses: Record<Status, string> = {
    Pass: 'bd-badge badge-success',
    Fail: 'bd-badge badge-danger',
};

const AssignmentStatusBadge: React.FC<{ status: Status }> = ({ status }) => {
    return <div className={badgeClasses[status]}>{status}</div>;
};

const ActionButtons: React.FC = () => (
    <div className="bd-button-action">
        <Link href="#" className="bd-default-tooltip edit">
            <span>
                <i className="fa-light fa-pen-to-square" />
            </span>
        </Link>
        <Link href="#" className="bd-default-tooltip view">
            <span>
                <i className="fa-sharp fa-light fa-eye" />
            </span>
        </Link>
        <Link href="#" className="bd-default-tooltip delete">
            <span>
                <i className="fa-light fa-trash-can" />
            </span>
        </Link>
    </div>
);

const StudentAssignmentMain: React.FC = () => (
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
                        {assignments.map(({ id, user, quiz, result, time, status }) => (
                            <tr key={id}>
                                <td>
                                    <p>{id}</p>
                                </td>
                                <td>
                                    <p>{user}</p>
                                </td>
                                <td>
                                    <p>{quiz}</p>
                                </td>
                                <td>
                                    <p>{result}</p>
                                </td>
                                <td>
                                    <p>{time}</p>
                                </td>
                                <td>
                                    <AssignmentStatusBadge status={status} />
                                </td>
                                <td>
                                    <ActionButtons />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
);

export default StudentAssignmentMain;
