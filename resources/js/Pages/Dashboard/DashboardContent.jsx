import React, { useState } from "react";
import ContentPanel from "../../Components/Table/ContentPanel";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from "chart.js";
import { Line } from "react-chartjs-2";
import Button from "../../Components/Table/Buttons/Button";
import { useTheme } from "../../Context/ThemeContext";
import {
    ArrowRight,
    Banknote,
    CalendarCheck,
    CheckCircle,
    CircleCheck,
    CircleX,
    Clock,
    Package,
    PackageSearch,
    ShoppingCart,
    Truck,
} from "lucide-react";
import { Link, usePage } from "@inertiajs/react";

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

const DashboardContent = ({ chartValues, statusesValues }) => {
    const { theme } = useTheme();
    const { auth } = usePage().props;
    const { admin_privileges } = auth.sessions;
    // FOR CHART
    const [timeFrame, setTimeFrame] = useState("daily");

    const dailyInfo = chartValues?.daily ?? [];
    const monthlyInfo = chartValues?.monthly ?? [];
    const yearlyInfo = chartValues?.yearly ?? [];

    // Sample data for different time frames with specific dates
    const dailyData = {
        labels: dailyInfo.labels,
        datasets: [
            {
                label: "Orders per Day",
                data: dailyInfo.data,
                borderColor: "#000", // black
                backgroundColor: "rgba(0, 0, 0, 0.1)", // semi-transparent black
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: "#000",
                pointBorderColor: "#fff",
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
            },
        ],
    };

    const monthlyData = {
        labels: monthlyInfo.labels,
        datasets: [
            {
                label: "Orders per Month",
                data: monthlyInfo.data,
                borderColor: "#000",
                backgroundColor: "rgba(0, 0, 0, 0.1)",
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: "#000",
                pointBorderColor: "#fff",
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
            },
        ],
    };

    const yearlyData = {
        labels: yearlyInfo.labels,
        datasets: [
            {
                label: "Orders per Year",
                data: yearlyInfo.data,
                borderColor: "#000",
                backgroundColor: "rgba(0, 0, 0, 0.1)",
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: "#000",
                pointBorderColor: "#fff",
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
            },
        ],
    };

    const getCurrentData = () => {
        switch (timeFrame) {
            case "daily":
                return dailyData;
            case "monthly":
                return monthlyData;
            case "yearly":
                return yearlyData;
            default:
                return dailyData;
        }
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            },
            title: {
                display: false,
            },
            tooltip: {
                backgroundColor: "rgba(0, 0, 0, 0.8)",
                titleColor: "#fff",
                bodyColor: "#fff",
                borderColor: "rgba(255, 255, 255, 0.1)",
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: true,
                callbacks: {
                    title: (context) => {
                        const timeFrameText =
                            timeFrame === "daily"
                                ? "Date"
                                : timeFrame === "monthly"
                                ? "Month"
                                : "Year";
                        return `${timeFrameText}: ${context[0].label}`;
                    },
                    label: (context) =>
                        `${
                            context.dataset.label
                        }: ${context.parsed.y.toLocaleString()} orders`,
                    afterLabel: (context) => {
                        if (timeFrame === "daily") {
                            const date = new Date();
                            date.setDate(
                                date.getDate() - (6 - context.dataIndex)
                            );
                            return `Full date: ${date.toLocaleDateString(
                                "en-US",
                                {
                                    weekday: "long",
                                    year: "numeric",
                                    month: "long",
                                    day: "numeric",
                                }
                            )}`;
                        }
                        return "";
                    },
                },
            },
        },
        scales: {
            x: {
                grid: {
                    display: false,
                },
                ticks: {
                    font: {
                        size: 10,
                    },
                    color: "#6B7280",
                },
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: "rgba(0, 0, 0, 0.1)",
                },
                ticks: {
                    font: {
                        size: 12,
                    },
                    color: "#6B7280",
                    callback: (value) => value.toLocaleString(),
                },
            },
        },
        interaction: {
            intersect: false,
            mode: "index",
        },
        elements: {
            point: {
                hoverBackgroundColor: "#fff",
            },
        },
    };

    const getTotalOrders = () => {
        const data = getCurrentData();
        return data.datasets[0].data.reduce((sum, value) => sum + value, 0);
    };

    const getAverageOrders = () => {
        const data = getCurrentData();
        const total = getTotalOrders();
        const length = data.datasets[0].data.length;

        return length === 0 ? 0 : Math.round(total / length);
    };

    // FOR STATUSES

    const stats = [
        {
            title: "Total Orders",
            value: statusesValues?.total_orders ?? 0,
            icon: ShoppingCart,
            color: "text-black",
            bgColor: "bg-black/10",
            privilege: [1, 2, 3, 4, 5, 6, 7],
        },
        {
            title: "For Payment Orders",
            value: statusesValues?.for_payment ?? 0,
            icon: Banknote,
            color: "text-cyan-600",
            bgColor: "bg-cyan-50",
            privilege: [1, 2, 3],
        },
        {
            title: "For Verification Orders",
            value: statusesValues?.for_verification ?? 0,
            icon: CheckCircle,
            color: "text-orange-600",
            bgColor: "bg-orange-50",
            privilege: [1, 3],
        },
        {
            title: "For Processing Orders",
            value: statusesValues?.for_processing ?? 0,
            icon: PackageSearch,
            color: "text-green-600",
            bgColor: "bg-green-50",
            privilege: [1, 3],
        },
        {
            title: "Incomplete Orders",
            value: statusesValues?.incomplete ?? 0,
            icon: CircleX,
            color: "text-red-600",
            bgColor: "bg-red-50",
            privilege: [1, 2, 3],
        },
        {
            title: "For Schedule Orders",
            value: statusesValues?.for_schedule ?? 0,
            icon: CalendarCheck,
            color: "text-blue-600",
            bgColor: "bg-blue-50",
            privilege: [1, 7],
        },
        {
            title: "For Delivery Orders",
            value: statusesValues?.for_delivery ?? 0,
            icon: Truck,
            color: "text-yellow-600",
            bgColor: "bg-yellow-50",
            privilege: [1, 7],
        },
        {
            title: "To Close Orders",
            value: statusesValues?.to_close ?? 0,
            icon: Clock,
            color: "text-purple-600",
            bgColor: "bg-purple-50",
            privilege: [1, 6],
        },
        {
            title: "Closed Orders",
            value: statusesValues?.closed ?? 0,
            icon: CircleCheck,
            color: "text-green-600",
            bgColor: "bg-green-50",
            privilege: [1, 6],
        },
    ];

    return (
        <ContentPanel>
            {/* ORDER STATUSES */}
            <div className="mb-5">
                <div className="flex items-center mb-2 space-x-3">
                    <p className="font-semibold text-lg">Order Analytics</p>
                    <Link
                        href="/orders"
                        className="text-xs text-gray-500 flex items-center space-x-2"
                    >
                        <ArrowRight className="w-2 h-2" />{" "}
                        <p>Go to Orders page</p>
                    </Link>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
                    {stats
                        .filter((stat) =>
                            stat.privilege.includes(admin_privileges)
                        )
                        .map((stat, index) => (
                            <div
                                key={index}
                                className="relative overflow-hidden rounded-lg shadow-sm border border-gray-200 transition-shadow duration-200 hover:shadow-md p-4"
                            >
                                <div className="flex flex-row items-center justify-between space-y-0">
                                    <p className="text-sm font-medium text-gray-600">
                                        {stat.title}
                                    </p>
                                    <div
                                        className={`p-2 rounded-md ${stat.bgColor}`}
                                    >
                                        {" "}
                                        {/* Simpler background, rounded-md */}
                                        <stat.icon
                                            className={`h-5 w-5 ${stat.color}`}
                                        />{" "}
                                        {/* Icon color from stat.color */}
                                    </div>
                                </div>
                                <div>
                                    <div className="text-xl font-bold text-gray-900 leading-none pb-2">
                                        {" "}
                                        {/* Smaller font, bold */}
                                        {stat.value}
                                    </div>
                                </div>
                            </div>
                        ))}
                </div>
            </div>
            <div className="border rounded-lg">
                {/* BUTTONS */}
                <div className="flex flex-wrap gap-1.5 p-4 border-b">
                    <Button
                        onClick={() => setTimeFrame("daily")}
                        fontColor={"text-white"}
                        extendClass={`${theme} min-w-20 flex justify-center`}
                    >
                        Daily
                    </Button>
                    <Button
                        onClick={() => setTimeFrame("monthly")}
                        fontColor={"text-white"}
                        extendClass={`${theme} min-w-20 flex justify-center`}
                    >
                        Monthly
                    </Button>
                    <Button
                        onClick={() => setTimeFrame("yearly")}
                        fontColor={"text-white"}
                        extendClass={`${theme} min-w-20 flex justify-center`}
                    >
                        Yearly
                    </Button>
                </div>
                {/* ORDER BREAKDOWN */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 px-5 mt-5">
                    <div className="border w-full rounded-lg hover:shadow-md">
                        <div className="p-4">
                            <div className="text-2xl font-bold text-blue-600">
                                {getTotalOrders().toLocaleString()}
                            </div>
                            <div className="text-sm text-gray-600">
                                Total Orders ({timeFrame})
                            </div>
                        </div>
                    </div>
                    <div className="border w-full rounded-lg hover:shadow-md">
                        <div className="p-4">
                            <div className="text-2xl font-bold text-green-600">
                                {getAverageOrders().toLocaleString()}
                            </div>
                            <div className="text-sm text-gray-600">
                                Average Orders
                            </div>
                        </div>
                    </div>
                    <div className="border w-full rounded-lg hover:shadow-md">
                        <div className="p-4">
                            <div className="text-2xl font-bold text-purple-600">
                                {getCurrentData().datasets[0].data.length}
                            </div>
                            <div className="text-sm text-gray-600">
                                Data Points
                            </div>
                        </div>
                    </div>
                </div>

                {/* ORDER CHART */}
                <div className="h-[400px] w-full p-5 pb-16">
                    <p className="font-semibold mb-5">
                        Orders{" "}
                        {timeFrame.charAt(0).toUpperCase() + timeFrame.slice(1)}{" "}
                        Overview
                    </p>
                    <Line data={getCurrentData()} options={options} />
                </div>
            </div>
        </ContentPanel>
    );
};

export default DashboardContent;
