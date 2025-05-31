@extends('layouts.appv2')

@section('content')
<div class="main-content">
    <div class="page-title">
        <i class="fas fa-clipboard-check"></i> New Vehicle Self-Check
    </div>
    
    <form id="selfCheckForm">
        <div class="form-section">
            <div class="form-section-title">1. Select Vehicle</div>
            
            <div class="vehicle-selector">
                <div class="vehicle-option selected">
                    <div class="vehicle-option-header">
                        <div class="vehicle-option-title">B 1234 XYZ</div>
                        <div class="vehicle-option-status status-active">Active</div>
                    </div>
                    <div class="vehicle-option-image">
                        <i class="fas fa-car"></i> Toyota Corolla (2019)
                    </div>
                    <div class="vehicle-option-details">
                        Last check: 3 months ago
                    </div>
                </div>
                
                <div class="vehicle-option">
                    <div class="vehicle-option-header">
                        <div class="vehicle-option-title">B 5678 ABC</div>
                        <div class="vehicle-option-status status-pending">Assessment Due</div>
                    </div>
                    <div class="vehicle-option-image">
                        <i class="fas fa-motorcycle"></i> Honda PCX (2021)
                    </div>
                    <div class="vehicle-option-details">
                        Last check: 5 months ago
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <div class="form-section-title">2. General Information</div>
            
            <div class="form-group">
                <label class="form-label">Assessment Date</label>
                <input type="date" class="form-control" value="2025-05-17">
            </div>
            
            <div class="form-group">
                <label class="form-label">Current Odometer Reading (km)</label>
                <input type="number" class="form-control" placeholder="e.g. 15000">
            </div>
            
            <div class="form-group">
                <label class="form-label">Assessment Location</label>
                <select class="form-control">
                    <option value="office">Office Parking Lot</option>
                    <option value="home">Home</option>
                    <option value="other">Other Location</option>
                </select>
            </div>
        </div>
        
        <div class="form-section">
            <div class="form-section-title">3. Vehicle Check Items</div>
            
            <div class="check-category">
                <!-- <div class="check-category-title">
                    <i class="fas fa-tachometer-alt"></i> External Inspection
                </div>
                 -->
                <div class="check-items">
                    <div class="check-item">
                        <div class="check-item-header">
                            <div class="check-item-title">Sistem Rem</div>
                        </div>
                        <div class="check-options">
                            <label class="check-option">
                                <input type="radio" name="body_condition" value="pass"> Baik
                            </label>
                            <label class="check-option">
                                <input type="radio" name="body_condition" value="fail"> Rusak
                            </label>
                            <!-- <label class="check-option">
                                <input type="radio" name="body_condition" value="na"> N/A
                            </label> -->
                        </div>
                        <!-- <div class="check-item-notes">
                            <div class="notes-toggle">
                                <i class="fas fa-plus-circle"></i> Add Notes
                            </div>
                            <div class="notes-input">
                                <textarea class="form-control" rows="2" placeholder="Enter notes here..."></textarea>
                            </div>
                        </div> -->
                    </div>
                    
                    <div class="check-item">
                        <div class="check-item-header">
                            <div class="check-item-title">Lampu utama dan sein</div>
                        </div>
                        <div class="check-options">
                            <label class="check-option">
                                <input type="radio" name="lights_signals" value="pass"> Baik 
                            </label>
                            <label class="check-option">
                                <input type="radio" name="lights_signals" value="fail"> Rusak
                            </label>
                            <!-- <label class="check-option">
                                <input type="radio" name="lights_signals" value="na"> N/A
                            </label> -->
                        </div>
                        <!-- <div class="check-item-notes">
                            <div class="notes-toggle">
                                <i class="fas fa-plus-circle"></i> Add Notes
                            </div>
                            <div class="notes-input">
                                <textarea class="form-control" rows="2" placeholder="Enter notes here..."></textarea>
                            </div>
                        </div> -->
                    </div>
                    
                    <div class="check-item">
                        <div class="check-item-header">
                            <div class="check-item-title">Ban & velg</div>
                        </div>
                        <div class="check-options">
                            <label class="check-option">
                                <input type="radio" name="windows_mirrors" value="pass"> Baik
                            </label>
                            <label class="check-option">
                                <input type="radio" name="windows_mirrors" value="fail"> Rusak
                            </label>
                            <!-- <label class="check-option">
                                <input type="radio" name="windows_mirrors" value="na"> N/A
                            </label> -->
                        </div>
                        <!-- <div class="check-item-notes">
                            <div class="notes-toggle">
                                <i class="fas fa-plus-circle"></i> Add Notes
                            </div>
                            <div class="notes-input">
                                <textarea class="form-control" rows="2" placeholder="Enter notes here..."></textarea>
                            </div>
                        </div> -->
                    </div>
                    
                    <div class="check-item">
                        <div class="check-item-header">
                            <div class="check-item-title">Oli Mesin</div>
                        </div>
                        <div class="check-options">
                            <label class="check-option">
                                <input type="radio" name="tires_condition" value="pass"> Baik
                            </label>
                            <label class="check-option">
                                <input type="radio" name="tires_condition" value="fail"> Rusak
                            </label>
                            <!-- <label class="check-option">
                                <input type="radio" name="tires_condition" value="na"> N/A
                            </label> -->
                        </div>
                        <!-- <div class="check-item-notes">
                            <div class="notes-toggle">
                                <i class="fas fa-plus-circle"></i> Add Notes
                            </div>
                            <div class="notes-input">
                                <textarea class="form-control" rows="2" placeholder="Enter notes here..."></textarea>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
    </form>
</div>
@endsection