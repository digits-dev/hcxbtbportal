import { Head } from "@inertiajs/react";
import ContentPanel from "../../Components/Table/ContentPanel";
import { Check, X } from 'lucide-react';


const ViewOrderDetails = ({ page_title, order, lines }) => {
  return (
    <>
      <Head title={page_title} />
      <ContentPanel>
        <div className="flex justify-center items-center">
          <div className="bg-white md:border border-gray-400 rounded-lg shadow-sm md:w-2/3">
             <div className="p-6 border-b border-gray-200">
                <div className="flex items-center justify-between">
                    <div>
                    <h2 className="text-2xl font-semibold text-gray-900">Customer Order Details</h2>
                    <p className="text-sm text-gray-600 mt-1">
                        Order #{order.reference_number} • Submitted on {new Date(order.created_at).toLocaleDateString()}
                    </p>
                    </div>
                    <div className="flex items-center gap-2">
                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Approved
                    </span>
                    </div>
                </div>
            </div>
             <div className="p-6">
                <div className="space-y-6">
                    {/* Customer Information Section */}
                    <div className="bg-gray-50 p-4 rounded-lg">
                        <h3 className="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {/* Customer Name */}
                            <div className="space-y-1">
                                <label className="block text-sm font-medium text-gray-700">Customer Name</label>
                                <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900">
                                    {order.customer_name}
                                </div>
                            </div>

                            {/* Email Address */}
                            <div className="space-y-1">
                                <label className="block text-sm font-medium text-gray-700">Email Address</label>
                                <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900">
                                    {order.email_address}
                                </div>
                            </div>

                             {/* Contact Details */}
                            <div className="space-y-1">
                                <label className="block text-sm font-medium text-gray-700">Contact Details</label>
                                <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900">
                                    {order.contact_details}
                                </div>
                            </div>

                            {/* Delivery Address */}
                            <div className="space-y-1">
                                <label className="block text-sm font-medium text-gray-700">Delivery Address</label>
                                <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900 whitespace-pre-line">
                                    {order.delivery_address}
                                </div>
                            </div>
                        </div>
                    </div>

                     {/* Financial Information Section */}
            <div className="bg-blue-50 p-4 rounded-lg">
              <h3 className="text-lg font-medium text-gray-900 mb-4">Downpayment Details</h3>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {/* Downpayment Status */}
                <div className="space-y-1">
                  <label className="block text-sm font-medium mb-2 text-gray-700">Downpayment</label>
                  <span
                    className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${
                        order.has_downpayment == 'yes' ? "bg-green-400 text-gray-700" : "bg-red-100 text-red-800"
                    }`}
                    >
                    {order.has_downpayment == 'yes' ? <Check size={12} /> : <X size={12} />}
                    {order.has_downpayment == 'yes' ? "Yes" : "No"}
                    </span>
                </div>

                {/* Downpayment Value */}
                {order.has_downpayment == 'yes' && (
                  <div className="space-y-1">
                    <label className="block text-sm font-medium text-gray-700">Downpayment Value</label>
                    <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900 font-medium">
                      {order.downpayment_value}
                    </div>
                  </div>
                )}

                {/* Financed Amount */}
                <div className="space-y-1">
                  <label className="block text-sm font-medium text-gray-700">Financed Amount</label>
                  <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900 font-medium">
                    {order.financed_amount}
                  </div>
                </div>
              </div>
            </div>

            {/* Item Information Section */}
          <div className="bg-purple-50 p-4 rounded-lg mb-4">
              <h3 className="text-lg font-medium text-gray-900 mb-4">Item Details</h3>
              <div className="overflow-x-auto bg-white border border-gray-200 rounded-lg">
                <table className="min-w-full divide-y divide-gray-200 text-sm text-left">
                  <thead className="bg-gray-100">
                    <tr>
                      <th className="px-4 py-2 font-medium text-gray-600">#</th>
                      <th className="px-4 py-2 font-medium text-gray-600">Description</th>
                      <th className="px-4 py-2 font-medium text-gray-600">Model</th>
                      <th className="px-4 py-2 font-medium text-gray-600">Color</th>
                      <th className="px-4 py-2 font-medium text-gray-600">Storage</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-100">
                    {lines.map((item, index) => (
                      <tr key={index}>
                        <td className="px-4 py-2 text-gray-700">{index + 1}</td>
                        <td className="px-4 py-2 text-gray-900">{item.item_description}</td>
                        <td className="px-4 py-2 text-gray-900">{item.model}</td>
                        <td className="px-4 py-2 text-gray-900">{item.actual_color}</td>
                        <td className="px-4 py-2 text-gray-900">{item.size}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

              {/* Contract Information Section */}
            <div className="bg-green-50 p-4 rounded-lg">
              <h3 className="text-lg font-medium text-gray-900 mb-4">Contract Information</h3>
              <div className="bg-white border border-gray-200 rounded-lg p-4">
                <div className="flex items-center gap-3">
                  <svg className="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={2}
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                  </svg>
                  <div className="flex-1">
                    <div className="font-medium text-gray-900">{order.approved_contract}</div>
                    <div className="text-sm text-gray-500">Approved contract document</div>
                  </div>
                  <button className="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors">
                    Download
                  </button>
                </div>
              </div>
            </div>
                    {/* Action Buttons */}
                  <div className="flex gap-4 pt-4 border-t border-gray-200">
                  <button
                      type="button"
                        onClick={() => window.location.href = '/orders'}
                      className="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                      
                  >
                      Back
                  </button>
              
                  </div>
                </div>
            </div>
          </div>
        </div>
      </ContentPanel>
    </>
  );
};

export default ViewOrderDetails;
