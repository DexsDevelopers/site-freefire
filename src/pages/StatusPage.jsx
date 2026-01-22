import React from 'react';
import { CheckCircle, XCircle, AlertTriangle, Server, Wifi } from 'lucide-react';
import { products } from '../data/products';

const StatusPage = () => {
  // Mock status data - in a real app this would come from an API
  const systemStatus = [
    { name: "Website", status: "online", uptime: "99.9%" },
    { name: "API de Pagamentos", status: "online", uptime: "100%" },
    { name: "Sistema de Entregas", status: "online", uptime: "99.8%" },
    { name: "Suporte via Discord", status: "online", uptime: "Online" },
  ];

  const getStatusColor = (status) => {
    switch (status) {
      case 'online': return 'text-green-500';
      case 'offline': return 'text-red-500';
      case 'maintenance': return 'text-yellow-500';
      default: return 'text-gray-500';
    }
  };

  const getStatusIcon = (status) => {
    switch (status) {
      case 'online': return <CheckCircle className="w-5 h-5 text-green-500" />;
      case 'offline': return <XCircle className="w-5 h-5 text-red-500" />;
      case 'maintenance': return <AlertTriangle className="w-5 h-5 text-yellow-500" />;
      default: return <AlertTriangle className="w-5 h-5 text-gray-500" />;
    }
  };

  return (
    <div className="min-h-screen pt-24 pb-12 px-4 sm:px-6 lg:px-8 bg-ff-black">
      <div className="max-w-5xl mx-auto">
        <h1 className="text-4xl md:text-5xl font-black text-white text-center mb-4 uppercase tracking-tighter">
          Status dos <span className="text-ff-red text-glow">Serviços</span>
        </h1>
        <p className="text-center text-gray-400 mb-12 max-w-2xl mx-auto">
          Acompanhe em tempo real a disponibilidade de nossos produtos e sistemas.
        </p>

        {/* System Status */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
          {systemStatus.map((sys, index) => (
            <div key={index} className="bg-[#0f0f0f] border border-white/5 p-6 rounded-xl flex flex-col items-center text-center hover:bg-[#151515] transition-colors">
              <div className="mb-3 p-3 bg-black/40 rounded-full border border-white/5">
                <Server className="w-6 h-6 text-gray-300" />
              </div>
              <h3 className="text-white font-bold mb-1">{sys.name}</h3>
              <div className="flex items-center gap-2 mt-2 bg-black/20 px-3 py-1 rounded-full border border-white/5">
                {getStatusIcon(sys.status)}
                <span className={`text-sm font-medium uppercase ${getStatusColor(sys.status)}`}>
                  {sys.status}
                </span>
              </div>
            </div>
          ))}
        </div>

        {/* Products Status */}
        <h2 className="text-2xl font-black text-white mb-6 uppercase tracking-wide flex items-center gap-2">
          <Wifi className="w-6 h-6 text-ff-red" />
          Status dos Cheats
        </h2>
        
        <div className="bg-[#0f0f0f] border border-white/5 rounded-xl overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left">
              <thead>
                <tr className="bg-black/40 border-b border-white/5">
                  <th className="p-4 text-gray-400 font-medium uppercase text-sm tracking-wider">Produto</th>
                  <th className="p-4 text-gray-400 font-medium uppercase text-sm tracking-wider">Status</th>
                  <th className="p-4 text-gray-400 font-medium uppercase text-sm tracking-wider">Última Atualização</th>
                  <th className="p-4 text-gray-400 font-medium uppercase text-sm tracking-wider">Versão</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-white/5">
                {products.map((product) => (
                  <tr key={product.id} className="hover:bg-white/5 transition-colors">
                    <td className="p-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-lg overflow-hidden bg-gray-800">
                          <img src={product.image} alt={product.name} className="w-full h-full object-cover" />
                        </div>
                        <span className="font-bold text-white">{product.name}</span>
                      </div>
                    </td>
                    <td className="p-4">
                      <div className="flex items-center gap-2">
                        {getStatusIcon(product.status === 'Ativo' ? 'online' : 'maintenance')}
                        <span className={product.status === 'Ativo' ? 'text-green-500' : 'text-yellow-500'}>
                          {product.status === 'Ativo' ? 'UNDETECTED' : 'MANUTENÇÃO'}
                        </span>
                      </div>
                    </td>
                    <td className="p-4 text-gray-400">Hoje, 10:00</td>
                    <td className="p-4 text-gray-400 font-mono text-xs">v{Math.floor(Math.random() * 10)}.0.{product.id}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};

export default StatusPage;
