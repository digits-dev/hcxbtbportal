import { Head, Link, router, usePage } from "@inertiajs/react";
import React, { useEffect, useState } from "react";
import ContentPanel from "../../Components/Table/ContentPanel";
import Tooltip from "../../Components/Tooltip/Tooltip";
import Button from "../../Components/Table/Buttons/Button";
import TopPanel from "../../Components/Table/TopPanel";
import Filters from "../../Components/Filters/Filters";
import Export from "../../Components/Table/Buttons/Export";
import { useTheme } from "../../Context/ThemeContext";
import useThemeStyles from "../../Hooks/useThemeStyles";
import TableSearch from "../../Components/Table/TableSearch";
import TableContainer from "../../Components/Table/TableContainer";
import Thead from "../../Components/Table/Thead";
import Row from "../../Components/Table/Row";
import TableHeader from "../../Components/Table/TableHeader";
import Tbody from "../../Components/Table/Tbody";
import RowStatus from "../../Components/Table/RowStatus";
import Pagination from "../../Components/Table/Pagination";
import RowData from "../../Components/Table/RowData";
import RowAction from "../../Components/Table/RowAction";
import CustomFilter from "../../Components/Table/Buttons/CustomFilter";
import moment from "moment/moment";
import Modal from "../../Components/Modal/Modal";
import StatusesAction from "./StatusesAction";

const Statuses = ({page_title, queryParams, statuses}) => {

    const { theme } = useTheme();
    const { auth } = usePage().props;
    const { primayActiveColor, textColorActive } = useThemeStyles(theme);
    const [pathname, setPathname] = useState(null);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [action, setAction] = useState(null);

    const [updateData, setUpdateData] = useState({
        id: "",
        name: "",
        color: "",
        status: "",
    });

    const handleModalClick = () => {
        setIsModalOpen(!isModalOpen);
    }

    useEffect(() => {
        const segments = window.location.pathname.split("/");
        setPathname(segments.pop());
    }, []);

    const refreshTable = (e) => {
        e.preventDefault();
        router.get(pathname);
    };

    return (
        <>
            <Head title={page_title}/>
            <ContentPanel>
                <TopPanel>
                    <div className="inline-flex flex-wrap gap-1">
                        <Tooltip text="Refresh data" arrow="bottom">
                            <Button
                                extendClass={
                                    (["bg-skin-white"].includes(theme)
                                        ? primayActiveColor
                                        : theme) + " py-[5px] px-[10px]"
                                }
                                fontColor={textColorActive}
                                onClick={refreshTable}
                            >
                                <i className="fa fa-rotate-right text-base p-[1px]"></i>
                            </Button>
                        </Tooltip>
                        {auth.access.isCreate &&
                            <Button
                                extendClass={
                                    (["bg-skin-white"].includes(theme)
                                        ? primayActiveColor
                                        : theme) + " py-[5px] px-[10px]"
                                }
                                type="button"
                                fontColor={textColorActive}
                                onClick={() => {
                                    handleModalClick();
                                    setAction("Add");
                                    setUpdateData({
                                        id: "",
                                        name: "",
                                        color: "",
                                        status: "",
                                    });
                                }}
                            >
                                <i className="fa-solid fa-plus mr-1"></i> Add Status
                            </Button>
                        }
                        <Export path="/item_masters/export" page_title={page_title}/>
                    </div>
                    <div className="flex">
                        <CustomFilter>
                            <Filters filter_inputs={[
                                {name: 'Status Name', column: 'name'},
                                {name: 'Color', column: 'color'},
                                {name: 'Created By', column: 'created_by'},
                                {name: 'Updated By', column: 'updated_by'},
                                {name: 'Created Date', column: 'created_at'},
                                {name: 'Updated Date', column: 'updated_at'},
                            ]}/>
                        </CustomFilter>
                        <TableSearch queryParams={queryParams} />
                    </div>
                </TopPanel>
                <TableContainer data={statuses?.data}>
                    <Thead>
                        <Row>
                            <TableHeader
                                sortable={false}
                                width="md"
                                justify="center"
                            >
                                Action
                            </TableHeader>
                            <TableHeader
                                name="status"
                                queryParams={queryParams}
                                width="md"
                            >
                                Status
                            </TableHeader>
                            <TableHeader
                                name="name"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Status Name
                            </TableHeader>
                            <TableHeader
                                name="color"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Color
                            </TableHeader>
                            <TableHeader
                                name="created_by"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Created By
                            </TableHeader>
                            <TableHeader
                                name="updated_by"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Updated By
                            </TableHeader>
                            <TableHeader
                                name="created_at"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Created Date
                            </TableHeader>
                            <TableHeader
                                name="updated_at"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Updated Date
                            </TableHeader>
                        </Row>
                    </Thead>
                    <Tbody data={statuses?.data}>
                        {statuses &&
                            statuses?.data.map((item, index) => (
                                <Row key={item.id}>
                                     <RowData center>
                                        {auth.access.isUpdate &&
                                            <RowAction
                                                type="button"
                                                action="edit"
                                                onClick={() => {
                                                    handleModalClick();
                                                    setAction("Update");
                                                    setUpdateData({
                                                        id: item.id,
                                                        name: item.name,
                                                        color: item.color,
                                                        status: item.status,
                                                    });
                                                }}
                                            />
                                        }
                                        <RowAction
                                            type="button"
                                            action="view"
                                            onClick={() => {
                                                handleModalClick();
                                                setAction("View");
                                                setUpdateData({
                                                    id: item.id,
                                                    name: item.name,
                                                    color: item.color,
                                                    status: item.status,
                                                });
                                            }}
                                        />
                                    </RowData>
                                    <RowStatus
                                        systemStatus={
                                            item.status === "ACTIVE"
                                                ? "active"
                                                : "inactive"
                                        }
                                    >
                                        {item.status === "ACTIVE"
                                            ? "ACTIVE"
                                            : "INACTIVE"}
                                    </RowStatus>
                                    <RowData>
                                        {item.name ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.color ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.get_created_by?.name ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.get_updated_by?.name ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.created_at ? (moment(item.created_at).format("YYYY-MM-DD HH:mm:ss")) : '-'}
                                    </RowData>
                                    <RowData>
                                        {item.updated_at ? (moment(item.updated_at).format("YYYY-MM-DD HH:mm:ss")) : '-'}
                                    </RowData>
                                </Row>
                            ))}
                    </Tbody>
                </TableContainer>
                <Pagination extendClass={theme} paginate={statuses} />
            </ContentPanel>
            <Modal
                theme={theme}
                show={isModalOpen}
                onClose={handleModalClick}
                title={
                    action == "Add"
                        ? "Add Status"
                        : action == "Update"
                        ? "Update Status"
                        : "Status Information"
                }
                width="xl"
                fontColor={textColorActive}
                btnIcon="fa fa-edit"
            >
                <StatusesAction
                    onClose={handleModalClick}
                    action={action}
                    updateData={updateData}
                />
            </Modal>
        </>
    );
};

export default Statuses;
