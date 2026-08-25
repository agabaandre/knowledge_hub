import React from 'react';

type ErrorMsgProps = {
    error?: string;
};

const ErrorMsg: React.FC<ErrorMsgProps> = ({ error }) => {
    if (!error) return null;
    return <p style={{ color: 'red', fontSize: "12px" }}>{error}</p>;
};

export default ErrorMsg;
