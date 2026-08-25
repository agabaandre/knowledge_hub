import React from "react";

 interface IProps {
  isOpen: boolean;
  videoUrl: string;
  onClose: () => void;
};

const VideoModal: React.FC<IProps> = ({ isOpen, videoUrl, onClose }) => {
  if (!isOpen) return null;

  return (
    <div id="video-overlay" className="video-overlay open" onClick={onClose}>
      <iframe
        src={videoUrl}
        frameBorder="0"
        allowFullScreen
        className="video-iframe"
        onClick={(e) => e.stopPropagation()}
      />
    </div>
  );
};

export default VideoModal;
