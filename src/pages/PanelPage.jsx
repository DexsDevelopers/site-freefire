import React, { useState } from 'react';
import { Shield, Zap, Target, Eye, Settings, Trash2 } from 'lucide-react';

const PanelPage = () => {
  const [activeTab, setActiveTab] = useState('AIM');
  const [toggles, setToggles] = useState({
    aim: {
      aimbotHead: false,
      aimbotLegit: false,
      noRecoil: false,
      flyHack: false,
      precision: false,
      scope2x: false,
      scope4x: false,
      atravessar: false
    },
    aim2: {
      m82b: false,
      m24: false,
      awmY: false,
      awm: false,
      vsk: false,
      switchAwm: false
    },
    visuals: {
      cameraHack: false,
      chams: false,
      security: false,
      streamMode: false
    },
    misc: {
      speed2x: false,
      aimFov: false,
      criticals: false,
      bypassMobile: false,
      hideTaskbar: false
    }
  });

  const handleToggle = (category, key) => {
    setToggles(prev => ({
      ...prev,
      [category]: {
        ...prev[category],
        [key]: !prev[category][key]
      }
    }));
  };

  const TabButton = ({ id, label }) => (
    <button
      onClick={() => setActiveTab(id)}
      className={`w-full py-3 px-6 rounded-lg font-black uppercase tracking-wider transition-all duration-300 mb-4 transform hover:scale-105 ${
        activeTab === id
          ? 'bg-white text-black shadow-[0_0_20px_rgba(255,255,255,0.5)] scale-105'
          : 'bg-ff-red text-white hover:bg-ff-dark-red shadow-[0_0_15px_rgba(255,0,0,0.3)]'
      }`}
    >
      {label}
    </button>
  );

  const Checkbox = ({ label, checked, onChange }) => (
    <div 
      onClick={onChange}
      className="flex items-center gap-4 cursor-pointer group p-2 hover:bg-white/5 rounded-lg transition-colors"
    >
      <div className={`w-6 h-6 border-2 flex items-center justify-center transition-all duration-300 ${
        checked 
          ? 'bg-ff-red border-ff-red shadow-[0_0_10px_rgba(255,0,0,0.5)]' 
          : 'border-ff-red group-hover:border-red-400'
      }`}>
        {checked && <div className="w-3 h-3 bg-white" />}
      </div>
      <span className={`font-bold uppercase tracking-wide transition-colors ${
        checked ? 'text-white' : 'text-gray-400 group-hover:text-white'
      }`}>
        {label}
      </span>
    </div>
  );

  return (
    <div className="min-h-screen bg-black pt-24 pb-12 px-4 flex items-center justify-center">
      <div className="w-full max-w-5xl aspect-video bg-black border border-ff-red relative shadow-[0_0_50px_rgba(255,0,0,0.1)] overflow-hidden flex flex-col md:flex-row rounded-xl">
        {/* Glow Effects */}
        <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-ff-red to-transparent opacity-50"></div>
        <div className="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-ff-red to-transparent opacity-50"></div>

        {/* Sidebar */}
        <div className="w-full md:w-1/4 border-r border-ff-red/30 p-6 flex flex-col relative bg-[#050505]">
          <div className="mb-12">
            <h2 className="text-white font-black text-xl tracking-tighter">
              THUNDER <span className="text-ff-red">STORE</span>
            </h2>
          </div>

          <div className="flex-grow space-y-2">
            <TabButton id="AIM" label="AIM" />
            <TabButton id="AIM2" label="AIM 2" />
            <TabButton id="VISUALS" label="VISUALS" />
            <TabButton id="MISC" label="MISC" />
          </div>

          <div className="mt-auto pt-6 border-t border-ff-red/10">
            <div className="text-gray-500 font-bold text-sm uppercase">Status</div>
            <div className="flex items-center gap-2 mt-1">
              <div className="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
              <span className="text-green-500 text-xs font-bold tracking-wider">UNDETECTED</span>
            </div>
          </div>
        </div>

        {/* Content Area */}
        <div className="flex-grow p-8 md:p-12 relative bg-[url('/img/grid.png')] bg-repeat opacity-90">
          <div className="absolute inset-0 bg-gradient-to-br from-ff-red/5 to-transparent pointer-events-none"></div>
          
          {/* Header for current tab */}
          <div className="mb-12 flex items-center gap-4">
             <div className="h-12 w-1 bg-ff-red shadow-[0_0_10px_rgba(255,0,0,0.8)]"></div>
             <div className="bg-[#111] py-2 px-8 rounded border border-white/5">
                <h2 className="text-2xl font-black text-white uppercase tracking-[0.2em]">{activeTab === 'AIM2' ? 'AIM 2' : activeTab}</h2>
             </div>
          </div>

          {/* Grid Content */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
            
            {activeTab === 'AIM' && (
              <>
                <div className="space-y-4">
                  <Checkbox label="Aimbot Head" checked={toggles.aim.aimbotHead} onChange={() => handleToggle('aim', 'aimbotHead')} />
                  <Checkbox label="Aimbot Legit" checked={toggles.aim.aimbotLegit} onChange={() => handleToggle('aim', 'aimbotLegit')} />
                  <Checkbox label="No Recoil" checked={toggles.aim.noRecoil} onChange={() => handleToggle('aim', 'noRecoil')} />
                  <Checkbox label="Fly Hack" checked={toggles.aim.flyHack} onChange={() => handleToggle('aim', 'flyHack')} />
                  <Checkbox label="Precision++ (BlackList)" checked={toggles.aim.precision} onChange={() => handleToggle('aim', 'precision')} />
                </div>
                <div className="space-y-4">
                  <Checkbox label="Scope 2x" checked={toggles.aim.scope2x} onChange={() => handleToggle('aim', 'scope2x')} />
                  <Checkbox label="Scope 4x" checked={toggles.aim.scope4x} onChange={() => handleToggle('aim', 'scope4x')} />
                  <Checkbox label="Atravessar" checked={toggles.aim.atravessar} onChange={() => handleToggle('aim', 'atravessar')} />
                </div>
              </>
            )}

            {activeTab === 'AIM2' && (
              <div className="space-y-4 col-span-2">
                <Checkbox label="Aimbot M82B" checked={toggles.aim2.m82b} onChange={() => handleToggle('aim2', 'm82b')} />
                <Checkbox label="Aimbot M24" checked={toggles.aim2.m24} onChange={() => handleToggle('aim2', 'm24')} />
                <Checkbox label="Aimbot AWM-Y" checked={toggles.aim2.awmY} onChange={() => handleToggle('aim2', 'awmY')} />
                <Checkbox label="Aimbot AWM" checked={toggles.aim2.awm} onChange={() => handleToggle('aim2', 'awm')} />
                <Checkbox label="Aimbot VSK" checked={toggles.aim2.vsk} onChange={() => handleToggle('aim2', 'vsk')} />
                <Checkbox label="Switch AWM" checked={toggles.aim2.switchAwm} onChange={() => handleToggle('aim2', 'switchAwm')} />
              </div>
            )}

            {activeTab === 'VISUALS' && (
              <div className="space-y-4 col-span-2">
                <Checkbox label="Camera Hack" checked={toggles.visuals.cameraHack} onChange={() => handleToggle('visuals', 'cameraHack')} />
                <Checkbox label="Chams" checked={toggles.visuals.chams} onChange={() => handleToggle('visuals', 'chams')} />
                <Checkbox label="Security" checked={toggles.visuals.security} onChange={() => handleToggle('visuals', 'security')} />
                <Checkbox label="Stream Mode" checked={toggles.visuals.streamMode} onChange={() => handleToggle('visuals', 'streamMode')} />
              </div>
            )}

            {activeTab === 'MISC' && (
              <div className="col-span-2">
                 <div className="mb-6 text-gray-400 font-mono text-sm">Tempo Restante: <span className="text-white font-bold">999 days</span></div>
                 <div className="space-y-4 mb-8">
                    <Checkbox label="Speed 2x" checked={toggles.misc.speed2x} onChange={() => handleToggle('misc', 'speed2x')} />
                    <Checkbox label="AimFOV" checked={toggles.misc.aimFov} onChange={() => handleToggle('misc', 'aimFov')} />
                    <Checkbox label="Criticals" checked={toggles.misc.criticals} onChange={() => handleToggle('misc', 'criticals')} />
                    <Checkbox label="Bypass Mobile" checked={toggles.misc.bypassMobile} onChange={() => handleToggle('misc', 'bypassMobile')} />
                    <Checkbox label="Hide Taskbar" checked={toggles.misc.hideTaskbar} onChange={() => handleToggle('misc', 'hideTaskbar')} />
                 </div>
                 
                 <button className="bg-[#111] hover:bg-[#1a1a1a] text-gray-400 hover:text-white border border-white/10 px-8 py-3 rounded-full font-bold uppercase tracking-widest transition-all flex items-center gap-2 group">
                    <Trash2 className="w-4 h-4 group-hover:text-ff-red transition-colors" />
                    Cleanner
                 </button>
              </div>
            )}

          </div>
        </div>
      </div>
    </div>
  );
};

export default PanelPage;
