import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Navbar from './components/Navbar';
import Footer from './components/Footer';
import HomePage from './pages/HomePage';
import RoulettePage from './pages/RoulettePage';
import StatusPage from './pages/StatusPage';
import TermsPage from './pages/TermsPage';
import DemoPage from './pages/DemoPage';
import FaqPage from './pages/FaqPage';
import PanelPage from './pages/PanelPage';
import { Globe, Headphones } from 'lucide-react';

function App() {
  return (
    <Router>
      <div className="min-h-screen flex flex-col bg-ff-black text-white font-sans">
        <Navbar />
        <main className="flex-grow">
          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route path="/roleta" element={<RoulettePage />} />
            <Route path="/status" element={<StatusPage />} />
            <Route path="/termos" element={<TermsPage />} />
            <Route path="/demonstracao" element={<DemoPage />} />
            <Route path="/faq" element={<FaqPage />} />
            <Route path="/painel" element={<PanelPage />} />
          </Routes>
        </main>
        <Footer />
        
        {/* Floating Action Buttons */}
        <div className="fixed bottom-8 left-8 z-50">
          <button className="bg-red-600/80 hover:bg-red-600 text-white p-3 rounded-full shadow-lg border-2 border-white/20 transition-all hover:scale-110">
            <Globe className="w-6 h-6" />
          </button>
        </div>
        
        <div className="fixed bottom-8 right-8 z-50">
          <button className="bg-ff-red hover:bg-ff-dark-red text-white p-4 rounded-full shadow-[0_0_20px_rgba(255,0,51,0.5)] transition-all hover:scale-110 animate-pulse">
            <Headphones className="w-8 h-8" />
          </button>
        </div>
      </div>
    </Router>
  );
}

export default App;
