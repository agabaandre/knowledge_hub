"use client";
import { createContext, useContext, useState, ReactNode } from "react";

interface VideoContextType {
  isVideoOpen: boolean;
  videoUrl: string;
  playVideo: (videoId: string, platform?: "youtube" | "vimeo") => void;
  closeVideo: () => void;
}

const VideoContext = createContext<VideoContextType | undefined>(undefined);

export const VideoProvider = ({ children }: { children: ReactNode }) => {
  const [isVideoOpen, setIsVideoOpen] = useState(false);
  const [videoUrl, setVideoUrl] = useState("");

  const playVideo = (videoId: string, platform: "youtube" | "vimeo" = "youtube") => {
    const url =
      platform === "youtube"
        ? `https://www.youtube.com/embed/${videoId}`
        : `https://player.vimeo.com/video/${videoId}`;
    setVideoUrl(url);
    setIsVideoOpen(true);
  };

  const closeVideo = () => {
    setIsVideoOpen(false);
    setVideoUrl("");
  };

  return (
    <VideoContext.Provider value={{ isVideoOpen, videoUrl, playVideo, closeVideo }}>
      {children}
    </VideoContext.Provider>
  );
};

export const useVideoModal = () => {
  const context = useContext(VideoContext);
  if (!context) {
    throw new Error("useVideoModal must be used within a VideoProvider");
  }
  return context;
};
