<template>
    <div class="quote-wrapper">
        <div class="modern-card">
            <div class="card-header-custom">
                <button @click="$emit('back')" class="back-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Back
                </button>
                <h2>Insurance Quote</h2>
            </div>

            <div class="card-body">
                <div class="client-type-selector mb-4">
                    <p class="selector-label">I am requesting an offer as a:</p>
                    <div class="selector-buttons">
                        <button
                            type="button"
                            class="type-btn"
                            :class="{ active: clientType === 'individual' }"
                            @click="setClientType('individual')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            Physical Person (Individual)
                        </button>
                        <button
                            type="button"
                            class="type-btn"
                            :class="{ active: clientType === 'company' }"
                            @click="setClientType('company')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                            Legal Entity (Company)
                        </button>
                    </div>
                </div>

                <form @submit.prevent="submitQuote">

                    <div v-if="providerStats" class="provider-stats mb-3">
                        <small>Received offers from {{ providerStats.successful }}/{{ providerStats.total }} providers</small>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title">Motor Details</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="input-wrapper">
                                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" v-model="formData.product.motor.startDate" class="custom-input" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-wrapper">
                                    <label class="form-label">Duration (Months) <span class="text-danger">*</span></label>
                                    <input type="number" v-model.number="formData.product.motor.termTime" class="custom-input" min="1" max="12" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-wrapper">
                                    <label class="form-label">Installments <span class="text-danger">*</span></label>
                                    <input type="number" v-model.number="formData.product.motor.installmentCount" class="custom-input" min="1" max="4" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title">Policyholder Information ({{ clientType === 'individual' ? 'Person' : 'Company' }})</h5>

                        <div class="row g-4">
                            <template v-if="clientType === 'company'">
                                <div class="col-md-12">
                                    <div class="input-wrapper">
                                        <label class="form-label">Business Name <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.policyHolder.businessName" class="custom-input" placeholder="e.g., Life is Hard SA" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-wrapper">
                                        <label class="form-label">Tax ID (CUI) <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.policyHolder.taxId" class="custom-input" placeholder="e.g., 19107160" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-wrapper">
                                        <label class="form-label">Registry No. (J) <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.policyHolder.companyRegistryNumber" class="custom-input" placeholder="e.g., J12/1403/2004" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-wrapper">
                                        <label class="form-label">CAEN Code <span class="text-danger">*</span></label>
                                        <input type="number" v-model.number="formData.product.policyHolder.caenCode" class="custom-input" placeholder="e.g., 6202" required />
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.policyHolder.firstName" class="custom-input" placeholder="e.g., Vasile" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.policyHolder.lastName" class="custom-input" placeholder="e.g., Pop" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">Tax ID (CNP) <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.policyHolder.taxId" class="custom-input" placeholder="e.g., 1910716..." required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">ID Number (CI) <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.policyHolder.identification.idNumber" class="custom-input" placeholder="e.g., CJ123456" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">Birthdate</label>
                                        <input type="date" v-model="formData.product.policyHolder.birthdate" class="custom-input" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">Gender</label>
                                        <select v-model="formData.product.policyHolder.gender" class="custom-input">
                                            <option value="">Select</option>
                                            <option value="m">Male</option>
                                            <option value="f">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="checkbox-wrapper">
                                        <input type="checkbox" id="ph_foreign" v-model="formData.product.policyHolder.isForeignPerson">
                                        <label for="ph_foreign">Is Foreign Person?</label>
                                    </div>
                                </div>
                            </template>

                            <div class="col-12 mt-3"><h6 class="subsection-title">Address & Contact</h6></div>
                            <div class="col-md-6">
                                <div class="input-wrapper">
                                    <label class="form-label">Email</label>
                                    <input type="email" v-model="formData.product.policyHolder.email" class="custom-input" placeholder="email@email.ro" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-wrapper">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="tel" v-model="formData.product.policyHolder.mobileNumber" class="custom-input" placeholder="e.g., 0744444444" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">County <span class="text-danger">*</span></label>
                                    <select v-model="formData.product.policyHolder.address.county" @change="onPolicyHolderCountyChange" class="custom-input" required>
                                        <option value="">Select County</option>
                                        <option v-for="county in counties" :key="county.code" :value="county.code">
                                            {{ county.name }} ({{ county.code }})
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">City <span class="text-danger">*</span></label>
                                    <select v-model="selectedPolicyHolderLocality" @change="onPolicyHolderLocalityChange" class="custom-input" required>
                                        <option value="">Select City</option>
                                        <option v-for="locality in localities" :key="locality.siruta" :value="locality">
                                            {{ locality.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">City Code (Siruta) <span class="text-danger">*</span></label>
                                    <input type="number" v-model.number="formData.product.policyHolder.address.cityCode" class="custom-input" readonly />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-wrapper">
                                    <label class="form-label">Street <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.policyHolder.address.street" class="custom-input" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-wrapper">
                                    <label class="form-label">House Number <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.policyHolder.address.houseNumber" class="custom-input" placeholder="e.g., 19-21" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-wrapper">
                                    <label class="form-label">Floor <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.policyHolder.address.floor" class="custom-input" placeholder="e.g., 3" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-wrapper">
                                    <label class="form-label">Building</label>
                                    <input type="text" v-model="formData.product.policyHolder.address.building" class="custom-input" placeholder="e.g., A1" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-wrapper">
                                    <label class="form-label">Staircase</label>
                                    <input type="text" v-model="formData.product.policyHolder.address.staircase" class="custom-input" placeholder="e.g., 1" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-wrapper">
                                    <label class="form-label">Apartment</label>
                                    <input type="text" v-model="formData.product.policyHolder.address.apartment" class="custom-input" placeholder="e.g., 12" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-wrapper">
                                    <label class="form-label">Postcode <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.policyHolder.address.postcode" class="custom-input" placeholder="e.g., 400356" required />
                                </div>
                            </div>

                            <template v-if="clientType === 'individual'">
                                <div class="col-12 mt-3"><h6 class="subsection-title">Driving License</h6></div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">Driving License Issue Date <span class="text-danger">*</span></label>
                                        <input type="date" v-model="formData.product.policyHolder.drivingLicense.issueDate" class="custom-input" required />
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title">Vehicle Owner Information</h5>
                        <div class="alert-info-custom mb-3">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="owner_same_as_policyholder" v-model="ownerSameAsPolicyholder" @change="syncOwnerWithPolicyholder">
                                <label for="owner_same_as_policyholder"><strong>Same as policyholder</strong></label>
                            </div>
                        </div>

                        <div v-if="!ownerSameAsPolicyholder" class="row g-4">
                            <template v-if="clientType === 'company'">
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">Business Name <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.vehicle.owner.businessName" class="custom-input" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">Tax ID (CUI) <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.vehicle.owner.taxId" class="custom-input" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">Registry No. (J) <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.vehicle.owner.companyRegistryNumber" class="custom-input" placeholder="e.g., J12/1403/2004" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">CAEN Code <span class="text-danger">*</span></label>
                                        <input type="number" v-model.number="formData.product.vehicle.owner.caenCode" class="custom-input" placeholder="e.g., 6202" required />
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <div class="col-md-4">
                                    <div class="input-wrapper">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.vehicle.owner.firstName" class="custom-input" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-wrapper">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.vehicle.owner.lastName" class="custom-input" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-wrapper">
                                        <label class="form-label">Tax ID (CNP) <span class="text-danger">*</span></label>
                                        <input type="text" v-model="formData.product.vehicle.owner.taxId" class="custom-input" required />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="checkbox-wrapper">
                                        <input type="checkbox" id="own_foreign" v-model="formData.product.vehicle.owner.isForeignPerson">
                                        <label for="own_foreign">Is Foreign Person?</label>
                                    </div>
                                </div>
                            </template>

                            <div class="col-12 mt-3"><h6 class="subsection-title">Owner Address</h6></div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">County <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.owner.address.county" class="custom-input" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.owner.address.city" class="custom-input" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">City Code <span class="text-danger">*</span></label>
                                    <input type="number" v-model.number="formData.product.vehicle.owner.address.cityCode" class="custom-input" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-wrapper">
                                    <label class="form-label">Street <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.owner.address.street" class="custom-input" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-wrapper">
                                    <label class="form-label">House Number <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.owner.address.houseNumber" class="custom-input" placeholder="e.g., 19-21" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-wrapper">
                                    <label class="form-label">Floor <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.owner.address.floor" class="custom-input" placeholder="e.g., 3" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Postcode <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.owner.address.postcode" class="custom-input" placeholder="e.g., 400356" required />
                                </div>
                            </div>

                            <template v-if="clientType === 'individual'">
                                <div class="col-12 mt-3"><h6 class="subsection-title">Owner Driving License</h6></div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">Driving License Issue Date <span class="text-danger">*</span></label>
                                        <input type="date" v-model="formData.product.vehicle.owner.drivingLicense.issueDate" class="custom-input" required />
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title">Vehicle Information</h5>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">License Plate <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.licensePlate" class="custom-input uppercase-input" placeholder="e.g., B101XXX" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">VIN <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.vin" class="custom-input uppercase-input" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Brand <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.brand" class="custom-input" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Model <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.model" class="custom-input" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Year <span class="text-danger">*</span></label>
                                    <input type="number" v-model.number="formData.product.vehicle.yearOfConstruction" class="custom-input" min="1900" :max="new Date().getFullYear() + 1" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Total Weight (kg) <span class="text-danger">*</span></label>
                                    <input type="number" v-model.number="formData.product.vehicle.totalWeight" class="custom-input" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                                    <select v-model="formData.product.vehicle.vehicleType" class="custom-input" required>
                                        <option value="">Select Type</option>
                                        <option v-for="vt in vehicleTypes" :key="vt.code || vt.id || vt" :value="vt.code || vt.id || vt">
                                            {{ vt.name || vt.label || vt }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Registration Type <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.registrationType" class="custom-input" placeholder="e.g., registered" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Fuel Type</label>
                                    <select v-model="formData.product.vehicle.fuelType" class="custom-input">
                                        <option value="">Select Fuel Type</option>
                                        <option value="diesel">Diesel</option>
                                        <option value="petrol">Petrol (Gasoline)</option>
                                        <option value="hybrid">Hybrid</option>
                                        <option value="electric">Electric</option>
                                        <option value="lpg">LPG</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Seats <span class="text-danger">*</span></label>
                                    <input type="number" v-model.number="formData.product.vehicle.seats" class="custom-input" min="1" placeholder="e.g., 5" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Engine Displacement (cc) <span class="text-danger">*</span></label>
                                    <input type="number" v-model.number="formData.product.vehicle.engineDisplacement" class="custom-input" min="0" placeholder="e.g., 1998" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Engine Power (kW) <span class="text-danger">*</span></label>
                                    <input type="number" v-model.number="formData.product.vehicle.enginePower" class="custom-input" min="0" placeholder="e.g., 110" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-wrapper">
                                    <label class="form-label">Usage Type <span class="text-danger">*</span></label>
                                    <select v-model="formData.product.vehicle.usageType" class="custom-input" required>
                                        <option value="personal">Personal</option>
                                        <option value="passengerTransportation">Passenger Transportation</option>
                                        <option value="taxi">Taxi</option>
                                        <option value="carRental">Car Rental</option>
                                        <option value="cargoTransportation">Cargo</option>
                                        <option value="distribution">Distribution</option>
                                        <option value="courier">Courier</option>
                                        <option value="drivingSchool">Driving School</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-wrapper">
                                    <label class="form-label">CIV Number <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formData.product.vehicle.identification.idNumber" class="custom-input" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">First Registration Date <span class="text-danger">*</span></label>
                                    <input type="date" v-model="formData.product.vehicle.firstRegistration" class="custom-input" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">Current Mileage (km) <span class="text-danger">*</span></label>
                                    <input type="number" v-model.number="formData.product.vehicle.currentMileage" class="custom-input" min="0" placeholder="e.g., 50000" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-wrapper">
                                    <label class="form-label">PTI Expiration Date <span class="text-danger">*</span></label>
                                    <input type="date" v-model="formData.product.additionalData.product.vehicle.expirationDatePti" class="custom-input" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title">Driver Information</h5>
                        <div v-for="(driver, index) in formData.product.vehicle.driver" :key="index" class="driver-card">
                            <h6 class="subsection-title">Driver {{ index + 1 }}</h6>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" v-model="driver.firstName" class="custom-input" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" v-model="driver.lastName" class="custom-input" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">Tax ID (CNP) <span class="text-danger">*</span></label>
                                        <input type="text" v-model="driver.taxId" class="custom-input" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-wrapper">
                                        <label class="form-label">ID Number <span class="text-danger">*</span></label>
                                        <input type="text" v-model="driver.identification.idNumber" class="custom-input" required />
                                    </div>
                                </div>
                            </div>
                            <button v-if="formData.product.vehicle.driver.length > 1" type="button" @click="removeDriver(index)" class="btn btn-danger mt-3">Remove Driver</button>
                        </div>
                        <button type="button" @click="addDriver" class="btn btn-secondary mt-3">+ Add Another Driver</button>
                    </div>

                    <div class="action-area">
                        <button type="submit" class="btn btn-brand w-100" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                            {{ loading ? 'Fetching Offers...' : 'Get Insurance Quotes' }}
                        </button>
                    </div>
                </form>

                <transition name="fade">
                    <div v-if="error" class="error-toast mt-4">
                        <span>{{ error }}</span>
                    </div>
                </transition>

                <div v-if="offers.length > 0" class="results-section mt-5">
                    <h4 class="results-title">Available Insurance Offers</h4>
                    <div class="row g-4">
                        <div v-for="(offer, index) in offers" :key="offer.offerId" class="col-lg-6">
                            <div class="offer-card">
                                <div class="offer-badge" :style="{ backgroundColor: getBadgeColor(index) }">{{ offer.providerName }}</div>
                                <div class="offer-content">
                                    <div class="offer-provider">{{ offer.providerBusinessName || offer.providerName }}</div>
                                    <div class="offer-price">
                                        <span class="currency">{{ offer.currency || 'RON' }}</span>
                                        <span class="amount">{{ offer.premiumAmount || 'N/A' }}</span>
                                        <span class="period">/year</span>
                                    </div>
                                    <div class="offer-details">
                                        <div class="detail-row">
                                            <span class="label">Period:</span>
                                            <span class="value">{{ offer.startDate }} - {{ offer.endDate }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Bonus-Malus:</span>
                                            <span class="value">{{ offer.bonusMalusClass || 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Commission:</span>
                                            <span class="value">{{ offer.commissionPercent }}% ({{ offer.commissionValue }} {{ offer.currency || 'RON' }})</span>
                                        </div>
                                        <div v-if="offer.installments && offer.installments.length > 1" class="detail-row">
                                            <span class="label">Installments:</span>
                                            <span class="value">{{ offer.installments.length }} payments</span>
                                        </div>
                                        <div v-if="offer.directCompensation" class="detail-row">
                                            <span class="label">Direct Compensation:</span>
                                            <span class="value">{{ offer.directCompensation.premiumAmount }} {{ offer.currency || 'RON' }}</span>
                                        </div>
                                    </div>
                                    <div class="offer-actions">
                                        <button type="button" @click="selectOffer(offer)" class="btn btn-primary">Select Offer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'InsuranceQuoteForm',
    props: {
        token: { type: String, required: true },
    },
    emits: ['back'],
data() {
    // Start date must be at least 2 days from now
    const startDate = new Date();
    startDate.setDate(startDate.getDate() + 2);
    const startDateStr = startDate.toISOString().split('T')[0];

    return {
        clientType: 'individual',
        providerStats: null,
        ownerSameAsPolicyholder: true, // Default to same as policyholder

        // Nomenclature data
        counties: [],
        localities: [],
        vehicleTypes: [],
        loadingNomenclature: false,
        nomenclatureError: null,
        selectedPolicyHolderLocality: null,
        selectedOwnerLocality: null,

        formData: {
            product: {
                motor: {
                    startDate: startDateStr,
                    termTime: 12,
                    installmentCount: 1,
                    commissionPercentLimit: 10,
                    generatePaymentLink: false,
                    webhookLink: '',
                    renewPolicy: null,
                },
                policyHolder: {
                    isForeignPerson: false,
                    firstName: '',
                    lastName: '',
                    taxId: '',
                    birthdate: '',
                    gender: '',
                    nationality: 'RO',
                    citizenship: 'RO',
                    email: '',
                    mobileNumber: '',
                    identification: {
                        idType: 'CI',
                        idNumber: '',
                        issueAuthority: '',
                        issueDate: ''
                    },
                    drivingLicense: {
                        issueDate: ''
                    },
                    businessName: '',
                    companyRegistryNumber: '',
                    caenCode: null,
                    address: {
                        country: 'RO',
                        county: '',
                        city: '',
                        cityCode: null,
                        street: '',
                        houseNumber: '',
                        building: '',
                        staircase: '',
                        apartment: '',
                        floor: '',
                        postcode: '',
                    },
                    hasDisability: false,
                    isRetired: false,
                },
                vehicle: {
                    licensePlate: '',
                    vin: '',
                    brand: '',
                    model: '',
                    yearOfConstruction: null,
                    vehicleType: '',
                    registrationType: 'registered',
                    fuelType: '',
                    engineDisplacement: null,
                    enginePower: null,
                    totalWeight: null,
                    seats: null,
                    usageType: 'personal',
                    currentMileage: null,
                    firstRegistration: '',
                    hasMobilityModifications: false,
                    isLeased: false,
                    isNew: false,
                    identification: {
                        idNumber: ''
                    },
                    owner: {
                        isForeignPerson: false,
                        firstName: '',
                        lastName: '',
                        taxId: '',
                        birthdate: '',
                        gender: '',
                        nationality: 'RO',
                        citizenship: 'RO',
                        email: '',
                        mobileNumber: '',
                        businessName: '',
                        companyRegistryNumber: '',
                        caenCode: null,
                        identification: {
                            idType: 'CI',
                            idNumber: '',
                            issueAuthority: '',
                            issueDate: ''
                        },
                        drivingLicense: {
                            issueDate: ''
                        },
                        address: {
                            country: 'RO',
                            county: '',
                            city: '',
                            cityCode: null,
                            street: '',
                            houseNumber: '',
                            building: '',
                            staircase: '',
                            apartment: '',
                            floor: '',
                            postcode: '',
                        },
                        hasDisability: false,
                        isRetired: false,
                    },
                    driver: [],
                },
                additionalData: {
                    product: {
                        vehicle: {
                            expirationDatePti: ''
                        }
                    }
                },
            },
        },
        loading: false,
        error: null,
        offers: [],
        badgeColors: ['#667eea', '#764ba2', '#f93b1d', '#059669', '#d97706'],
    };
},
    async mounted() {
        // Fetch nomenclature data when component loads
        await this.fetchNomenclatureData();
    },
    methods: {
        async fetchNomenclatureData() {
            this.loadingNomenclature = true;
            this.nomenclatureError = null;

            try {
                // Fetch counties
                const countiesRes = await fetch('/api/nomenclature/counties', {
                    headers: {
                        'Authorization': `Bearer ${this.token}`,
                        'Accept': 'application/json',
                    },
                });
                if (countiesRes.ok) {
                    const countiesData = await countiesRes.json();
                    this.counties = countiesData.data || [];
                    console.log('Counties loaded:', this.counties.length);
                }

                // Fetch vehicle types - use fallback if API doesn't have this endpoint
                const vehicleTypesRes = await fetch('/api/nomenclature/vehicle-types', {
                    headers: {
                        'Authorization': `Bearer ${this.token}`,
                        'Accept': 'application/json',
                    },
                });
                if (vehicleTypesRes.ok) {
                    const vehicleTypesData = await vehicleTypesRes.json();
                    if (vehicleTypesData.data && vehicleTypesData.data.length > 0) {
                        this.vehicleTypes = vehicleTypesData.data;
                    } else {
                        // Use fallback vehicle types
                        this.vehicleTypes = this.getDefaultVehicleTypes();
                    }
                    console.log('Vehicle types loaded:', this.vehicleTypes);
                } else {
                    // Use fallback vehicle types if API fails
                    this.vehicleTypes = this.getDefaultVehicleTypes();
                    console.log('Using fallback vehicle types:', this.vehicleTypes);
                }

                // Fetch localities for default county (CJ)
                await this.fetchLocalities('CJ');

            } catch (err) {
                console.error('Error fetching nomenclature:', err);
                this.nomenclatureError = 'Failed to load form data';
            } finally {
                this.loadingNomenclature = false;
            }
        },

        async fetchLocalities(countyCode) {
            try {
                const res = await fetch(`/api/nomenclature/localities/${countyCode}`, {
                    headers: {
                        'Authorization': `Bearer ${this.token}`,
                        'Accept': 'application/json',
                    },
                });
                if (res.ok) {
                    const data = await res.json();
                    this.localities = data.data || [];
                    console.log('Localities loaded for', countyCode, ':', this.localities.length);

                    // Auto-select first locality if available and update form
                    if (this.localities.length > 0) {
                        const firstLocality = this.localities[0];
                        console.log('Sample locality:', firstLocality);
                    }
                }
            } catch (err) {
                console.error('Error fetching localities:', err);
            }
        },

        async onCountyChange(event) {
            const countyCode = event.target.value;
            if (countyCode) {
                await this.fetchLocalities(countyCode);
            }
        },

        async onPolicyHolderCountyChange(event) {
            const countyCode = event.target.value;
            if (countyCode) {
                await this.fetchLocalities(countyCode);
                // Also update owner county if same as policyholder
                this.formData.product.vehicle.owner.address.county = countyCode;
            }
        },

        onPolicyHolderLocalityChange() {
            if (this.selectedPolicyHolderLocality) {
                const loc = this.selectedPolicyHolderLocality;
                this.formData.product.policyHolder.address.city = loc.name;
                this.formData.product.policyHolder.address.cityCode = loc.siruta || loc.id;
                // Also update owner address if same as policyholder
                this.formData.product.vehicle.owner.address.city = loc.name;
                this.formData.product.vehicle.owner.address.cityCode = loc.siruta || loc.id;
                this.selectedOwnerLocality = loc;
            }
        },

        onOwnerLocalityChange() {
            if (this.selectedOwnerLocality) {
                const loc = this.selectedOwnerLocality;
                this.formData.product.vehicle.owner.address.city = loc.name;
                this.formData.product.vehicle.owner.address.cityCode = loc.siruta || loc.id;
            }
        },

        syncOwnerWithPolicyholder() {
            if (this.ownerSameAsPolicyholder) {
                // Copy all relevant policyholder data to owner
                const ph = this.formData.product.policyHolder;
                const owner = this.formData.product.vehicle.owner;

                // Copy common fields
                owner.taxId = ph.taxId;
                owner.email = ph.email;
                owner.mobileNumber = ph.mobileNumber;

                // Copy address
                owner.address = { ...ph.address };

                if (this.clientType === 'company') {
                    // Copy company-specific fields
                    owner.businessName = ph.businessName;
                    owner.companyRegistryNumber = ph.companyRegistryNumber;
                    owner.caenCode = ph.caenCode;
                } else {
                    // Copy individual-specific fields
                    owner.firstName = ph.firstName;
                    owner.lastName = ph.lastName;
                    owner.isForeignPerson = ph.isForeignPerson;
                    owner.birthdate = ph.birthdate;
                    owner.gender = ph.gender;
                    owner.nationality = ph.nationality;
                    owner.citizenship = ph.citizenship;
                    owner.identification = { ...ph.identification };
                    owner.drivingLicense = { ...ph.drivingLicense };
                    owner.hasDisability = ph.hasDisability;
                    owner.isRetired = ph.isRetired;
                }
            }
        },

        setClientType(type) {
            this.clientType = type;
            // Reset form data when switching client type to avoid data overlap
            this.resetFormDataForClientType(type);
        },

        resetFormDataForClientType(type) {
            // Reset policyholder fields based on type
            if (type === 'company') {
                // Clear individual-specific fields
                this.formData.product.policyHolder.firstName = '';
                this.formData.product.policyHolder.lastName = '';
                this.formData.product.policyHolder.birthdate = '';
                this.formData.product.policyHolder.gender = '';
                this.formData.product.policyHolder.isForeignPerson = false;
                this.formData.product.policyHolder.identification = {
                    idType: 'CI',
                    idNumber: '',
                    issueAuthority: '',
                    issueDate: ''
                };
                this.formData.product.policyHolder.drivingLicense = { issueDate: '' };
                // Reset owner individual fields too
                this.formData.product.vehicle.owner.firstName = '';
                this.formData.product.vehicle.owner.lastName = '';
                this.formData.product.vehicle.owner.birthdate = '';
                this.formData.product.vehicle.owner.gender = '';
                this.formData.product.vehicle.owner.isForeignPerson = false;
                this.formData.product.vehicle.owner.identification = {
                    idType: 'CI',
                    idNumber: '',
                    issueAuthority: '',
                    issueDate: ''
                };
                this.formData.product.vehicle.owner.drivingLicense = { issueDate: '' };
            } else {
                // Clear company-specific fields
                this.formData.product.policyHolder.businessName = '';
                this.formData.product.policyHolder.companyRegistryNumber = '';
                this.formData.product.policyHolder.caenCode = null;
                // Reset owner company fields too
                this.formData.product.vehicle.owner.businessName = '';
                this.formData.product.vehicle.owner.companyRegistryNumber = '';
                this.formData.product.vehicle.owner.caenCode = null;
            }
        },

        buildIndividualPerson(data) {
            // Build a person object for physical person (individual)
            const person = {
                isForeignPerson: data.isForeignPerson || false,
                firstName: data.firstName,
                lastName: data.lastName,
                taxId: data.taxId,
                email: data.email,
                mobileNumber: data.mobileNumber,
                address: this.buildAddress(data.address),
                hasDisability: data.hasDisability || false,
                isRetired: data.isRetired || false,
            };

            // Add optional fields only if they have values
            if (data.birthdate) person.birthdate = data.birthdate;
            if (data.gender) person.gender = data.gender;
            if (data.nationality) person.nationality = data.nationality;
            if (data.citizenship) person.citizenship = data.citizenship;

            // Add identification for individuals (required)
            if (data.identification && data.identification.idNumber) {
                person.identification = {
                    idType: data.identification.idType || 'CI',
                    idNumber: data.identification.idNumber,
                };
                if (data.identification.issueAuthority) {
                    person.identification.issueAuthority = data.identification.issueAuthority;
                }
                if (data.identification.issueDate) {
                    person.identification.issueDate = data.identification.issueDate;
                }
            }

            // Add driving license if available
            if (data.drivingLicense && data.drivingLicense.issueDate) {
                person.drivingLicense = { issueDate: data.drivingLicense.issueDate };
            }

            return person;
        },

        buildCompanyPerson(data) {
            // Build a person object for legal entity (company)
            const person = {
                businessName: data.businessName,
                taxId: data.taxId,
                email: data.email,
                mobileNumber: data.mobileNumber,
                address: this.buildAddress(data.address),
            };

            // Add company-specific fields
            if (data.companyRegistryNumber) {
                person.companyRegistryNumber = data.companyRegistryNumber;
            }
            if (data.caenCode) {
                person.caenCode = parseInt(data.caenCode, 10);
            }

            return person;
        },

        buildAddress(addr) {
            const address = {
                country: addr.country || 'RO',
                county: addr.county,
                city: addr.city,
                cityCode: parseInt(addr.cityCode, 10),
                street: addr.street,
            };

            // Add optional address fields only if they have values
            if (addr.houseNumber) address.houseNumber = addr.houseNumber;
            if (addr.building) address.building = addr.building;
            if (addr.staircase) address.staircase = addr.staircase;
            if (addr.apartment) address.apartment = addr.apartment;
            if (addr.floor) address.floor = addr.floor;
            if (addr.postcode) address.postcode = addr.postcode;

            return address;
        },

        preparePayload() {
            const motor = { ...this.formData.product.motor };

            // Remove null/empty optional motor fields
            if (!motor.webhookLink) delete motor.webhookLink;
            if (!motor.renewPolicy) delete motor.renewPolicy;

            // Build policyholder based on client type
            let policyHolder;
            let vehicleOwner;

            if (this.clientType === 'company') {
                policyHolder = this.buildCompanyPerson(this.formData.product.policyHolder);
                // Use policyholder data for owner if same, otherwise use owner form data
                if (this.ownerSameAsPolicyholder) {
                    vehicleOwner = this.buildCompanyPerson(this.formData.product.policyHolder);
                } else {
                    vehicleOwner = this.buildCompanyPerson(this.formData.product.vehicle.owner);
                }
            } else {
                policyHolder = this.buildIndividualPerson(this.formData.product.policyHolder);
                // Use policyholder data for owner if same, otherwise use owner form data
                if (this.ownerSameAsPolicyholder) {
                    vehicleOwner = this.buildIndividualPerson(this.formData.product.policyHolder);
                } else {
                    vehicleOwner = this.buildIndividualPerson(this.formData.product.vehicle.owner);
                }
            }

            // Build vehicle object
            const vehicle = {
                licensePlate: this.formData.product.vehicle.licensePlate,
                vin: this.formData.product.vehicle.vin,
                brand: this.formData.product.vehicle.brand,
                model: this.formData.product.vehicle.model,
                yearOfConstruction: parseInt(this.formData.product.vehicle.yearOfConstruction, 10),
                vehicleType: this.formData.product.vehicle.vehicleType,
                registrationType: this.formData.product.vehicle.registrationType,
                totalWeight: parseInt(this.formData.product.vehicle.totalWeight, 10),
                usageType: this.formData.product.vehicle.usageType,
                identification: { idNumber: this.formData.product.vehicle.identification.idNumber },
                owner: vehicleOwner,
                driver: this.formData.product.vehicle.driver.map(d => ({
                    firstName: d.firstName,
                    lastName: d.lastName,
                    taxId: d.taxId,
                    identification: { idNumber: d.identification.idNumber },
                    ...(d.mobileNumber ? { mobileNumber: d.mobileNumber } : {}),
                })),
            };

            // Add optional vehicle fields
            if (this.formData.product.vehicle.fuelType) {
                vehicle.fuelType = this.formData.product.vehicle.fuelType;
            }
            if (this.formData.product.vehicle.engineDisplacement) {
                vehicle.engineDisplacement = parseInt(this.formData.product.vehicle.engineDisplacement, 10);
            }
            if (this.formData.product.vehicle.enginePower) {
                vehicle.enginePower = parseInt(this.formData.product.vehicle.enginePower, 10);
            }
            if (this.formData.product.vehicle.seats) {
                vehicle.seats = parseInt(this.formData.product.vehicle.seats, 10);
            }
            if (this.formData.product.vehicle.firstRegistration) {
                vehicle.firstRegistration = this.formData.product.vehicle.firstRegistration;
            }
            if (this.formData.product.vehicle.currentMileage) {
                vehicle.currentMileage = parseInt(this.formData.product.vehicle.currentMileage, 10);
            }
            if (typeof this.formData.product.vehicle.hasMobilityModifications === 'boolean') {
                vehicle.hasMobilityModifications = this.formData.product.vehicle.hasMobilityModifications;
            }
            if (typeof this.formData.product.vehicle.isLeased === 'boolean') {
                vehicle.isLeased = this.formData.product.vehicle.isLeased;
            }
            if (typeof this.formData.product.vehicle.isNew === 'boolean') {
                vehicle.isNew = this.formData.product.vehicle.isNew;
            }

            // Build final payload
            const payload = {
                product: {
                    motor,
                    policyholder: policyHolder,
                    vehicle,
                },
            };

            // Add additionalData only if it exists and has content
            if (this.formData.product.additionalData) {
                payload.product.additionalData = this.formData.product.additionalData;
            }

            console.log('Prepared payload for', this.clientType, ':', JSON.stringify(payload, null, 2));
            return payload;
        },

        getDefaultVehicleTypes() {
            // Standard EU vehicle categories used in Romanian RCA
            return [
                { code: 'A1', name: 'A1 - Moped/Motorcycle ≤125cc' },
                { code: 'A2', name: 'A2 - Motorcycle ≤35kW' },
                { code: 'A3', name: 'A3 - Motorcycle >35kW' },
                { code: 'A4', name: 'A4 - Motorcycle with sidecar' },
                { code: 'A5', name: 'A5 - Three-wheeled vehicle' },
                { code: 'M1', name: 'M1 - Passenger car (≤8+1 seats)' },
                { code: 'M1G', name: 'M1G - Off-road passenger car' },
                { code: 'M2', name: 'M2 - Minibus (≤5t, >8+1 seats)' },
                { code: 'M3', name: 'M3 - Bus (>5t, >8+1 seats)' },
                { code: 'N1', name: 'N1 - Light goods vehicle (≤3.5t)' },
                { code: 'N1G', name: 'N1G - Off-road light goods vehicle' },
                { code: 'N2', name: 'N2 - Medium goods vehicle (3.5-12t)' },
                { code: 'N3', name: 'N3 - Heavy goods vehicle (>12t)' },
                { code: 'O1', name: 'O1 - Light trailer (≤0.75t)' },
                { code: 'O2', name: 'O2 - Trailer (0.75-3.5t)' },
                { code: 'O3', name: 'O3 - Trailer (3.5-10t)' },
                { code: 'O4', name: 'O4 - Heavy trailer (>10t)' },
                { code: 'L1e', name: 'L1e - Light two-wheel vehicle' },
                { code: 'L2e', name: 'L2e - Three-wheel moped' },
                { code: 'L3e', name: 'L3e - Two-wheel motorcycle' },
                { code: 'L4e', name: 'L4e - Motorcycle with sidecar' },
                { code: 'L5e', name: 'L5e - Motor tricycle' },
                { code: 'L6e', name: 'L6e - Light quadricycle' },
                { code: 'L7e', name: 'L7e - Heavy quadricycle' },
                { code: 'T1', name: 'T1 - Agricultural tractor' },
                { code: 'T2', name: 'T2 - Tracked tractor' },
                { code: 'T3', name: 'T3 - Low-speed tractor' },
            ];
        },

        async submitQuote() {
            this.loading = true;
            this.error = null;
            this.offers = [];
            this.providerStats = null;

            try {
                const payload = this.preparePayload();

                const response = await fetch('/api/offer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.token}`,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (!response.ok) {
                    if(data.errors) {
                        this.error = Object.values(data.errors).flat().join(', ');
                    } else if (data.details) {
                        this.error = JSON.stringify(data.details);
                    } else {
                        this.error = data.error || data.message || 'Failed to retrieve quotes';
                    }
                    console.error("API ERROR:", data);
                    return;
                }

                this.offers = data.data?.offers || [];
                this.providerStats = {
                    total: data.data?.totalProviders || 0,
                    successful: data.data?.successfulProviders || 0,
                };

                if (this.offers.length === 0) {
                    this.error = 'No quotes available from any provider';
                }
            } catch (err) {
                console.error(err);
                this.error = err.message || 'An error occurred';
            } finally {
                this.loading = false;
            }
        },
        addDriver() {
            this.formData.product.vehicle.driver.push({
                firstName: '',
                lastName: '',
                taxId: '',
                identification: { idNumber: '' },
            });
        },
        removeDriver(index) {
            this.formData.product.vehicle.driver.splice(index, 1);
        },
        selectOffer(offer) {
            console.log('Selected offer:', offer);
        },
        getBadgeColor(index) {
            return this.badgeColors[index % this.badgeColors.length];
        },
    },
};
</script>

<style scoped>
/* Main Wrapper */
.quote-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    padding: 15px;
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: #334155;
}

/* Card Styling */
.modern-card {
    background: white;
    width: 100%;
    max-width: 1200px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.card-header-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px 30px;
    color: white;
    display: flex;
    align-items: center;
    gap: 15px;
}

.card-header-custom h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

/* Back Button */
.back-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.9rem;
    transition: background 0.2s;
}
.back-btn:hover { background: rgba(255, 255, 255, 0.3); }

.card-body {
    padding: 30px;
    max-height: 85vh;
    overflow-y: auto;
}

/* Client Type Selector (Big Buttons) */
.client-type-selector {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
}

.selector-label {
    margin-bottom: 12px;
    font-weight: 600;
    color: #64748b;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.selector-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.type-btn {
    display: flex;
    align-items: center;
    padding: 12px 24px;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 8px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.type-btn:hover {
    border-color: #cbd5e1;
    background: #f1f5f9;
}

.type-btn.active {
    border-color: #667eea;
    background: #eff6ff;
    color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.type-btn svg { margin-right: 8px; }

/* Form Elements */
.form-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f1f5f9;
}
.form-section:last-child { border-bottom: none; }

.section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 15px;
}

.subsection-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: #94a3b8;
    margin-bottom: 10px;
    text-transform: uppercase;
}

.input-wrapper { display: flex; flex-direction: column; }

.form-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 6px;
    text-transform: uppercase;
}

.custom-input, select {
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.95rem;
    background-color: #fff;
    width: 100%;
    transition: border 0.2s;
}

.custom-input:focus, select:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.text-danger { color: #ef4444; }
.alert-info-custom { font-size: 0.85rem; color: #64748b; background: #f1f5f9; padding: 8px 12px; border-radius: 6px; }

/* Error Toast */
.error-toast {
    background-color: #fef2f2;
    color: #dc2626;
    padding: 14px 16px;
    border-radius: 8px;
    font-size: 0.9rem;
    border: 1px solid #fecaca;
    display: flex;
    align-items: center;
}

/* Grid System */
.row { display: flex; flex-wrap: wrap; margin: 0 -10px; }
.row > * { padding: 0 10px; margin-bottom: 15px; }
.col-12 { flex: 0 0 100%; }
.col-md-3 { flex: 0 0 25%; }
.col-md-4 { flex: 0 0 33.333%; }
.col-md-6 { flex: 0 0 50%; }
.col-md-8 { flex: 0 0 66.666%; }
.col-md-12 { flex: 0 0 100%; }
.col-lg-6 { flex: 0 0 50%; }

@media (max-width: 768px) {
    .col-md-3, .col-md-4, .col-md-6, .col-md-8 { flex: 0 0 100%; }
    .selector-buttons { flex-direction: column; }
    .type-btn { justify-content: center; }
}

/* Submit Button */
.btn-brand {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 14px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: transform 0.2s;
}
.btn-brand:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(118, 75, 162, 0.25); }
.btn-brand:disabled { opacity: 0.7; cursor: not-allowed; }

/* Driver Card */
.driver-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

/* Offer Cards */
.results-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #e2e8f0;
}

.results-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 20px;
}

.offer-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    height: 100%;
}

.offer-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.offer-badge {
    padding: 8px 16px;
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.offer-content {
    padding: 20px;
}

.offer-provider {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 10px;
}

.offer-price {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 15px;
}

.offer-price .currency {
    font-size: 0.9rem;
    color: #64748b;
}

.offer-price .amount {
    font-size: 2rem;
    font-weight: 700;
    color: #059669;
}

.offer-price .period {
    font-size: 0.85rem;
    color: #94a3b8;
}

.offer-details {
    background: #f8fafc;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 15px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.85rem;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-row .label {
    color: #64748b;
    font-weight: 500;
}

.detail-row .value {
    color: #1e293b;
    font-weight: 600;
}

.offer-actions {
    margin-top: 10px;
}

.offer-actions .btn-primary {
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 12px;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s;
}

.offer-actions .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
